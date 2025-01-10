<?php /** @noinspection PhpUnused */

/**
 * Created by PhpStorm.
 * User: Andriy
 * Date: 18.06.2021
 * Time: 19:55
 * Made with <3 by West from Bubuni Team
 */

namespace App\Controller;


use App\PrintableException;
use PDO;

class Demo extends AbstractController
{
    private const DEFAULT_ITEMS_PER_PAGE = 10;
    private const ALLOWED_PER_PAGE = [10, 25, 50];

    public function actionIndex(): string
    {
        $db = $this->db();
        $page = max(1, (int)($this->getFromRequest('page') ?? 1));
        $search = $this->getFromRequest('search');
        $playerId = $this->getFromRequest('find');
        $perPage = (int)($this->getFromRequest('per_page') ?? self::DEFAULT_ITEMS_PER_PAGE);
        $dateSearch = $this->getFromRequest('date');

        // Validate per_page parameter
        if (!in_array($perPage, self::ALLOWED_PER_PAGE)) {
            $perPage = self::DEFAULT_ITEMS_PER_PAGE;
        }   

        // Build base query
        $baseQuery = "FROM `record` r 
                     LEFT JOIN `record_player` rp ON r.record_id = rp.record_id";
        
        $whereConditions = [];
        $params = [];

        // Add search conditions
        if ($search) {
            // Check if search is a date format (DD.MM)
            if (preg_match('/^\d{1,2}\.\d{1,2}$/', $search)) {
                list($day, $month) = explode('.', $search);
                $whereConditions[] = "DATE_FORMAT(FROM_UNIXTIME(r.uploaded_at), '%d.%m') = :date";
                $params[':date'] = sprintf('%02d.%02d', $day, $month);
            } else {
                // Regular search conditions
                $whereConditions[] = "(r.map LIKE :search OR rp.username LIKE :search OR rp.account_id = :account_id)";
                $params[':search'] = "%$search%";
                $params[':account_id'] = is_numeric($search) ? (int)$search : -1;
            }
        }

        // Add date search condition
        if ($dateSearch) {
            if (preg_match('/^\d{1,2}\.\d{1,2}$/', $dateSearch)) {
                list($day, $month) = explode('.', $dateSearch);
                $whereConditions[] = "DATE_FORMAT(FROM_UNIXTIME(r.uploaded_at), '%d.%m') = :date";
                $params[':date'] = sprintf('%02d.%02d', $day, $month);
            }
        }

        // Add player filter
        if ($playerId) {
            $whereConditions[] = "rp.account_id = :playerId";
            $params[':playerId'] = $playerId;
        }

        $whereClause = !empty($whereConditions) ? "WHERE " . implode(' AND ', $whereConditions) : "";

        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(DISTINCT r.record_id) as total " . $baseQuery . " " . $whereClause);
        $countStmt->execute($params);
        $result = $countStmt->fetch(PDO::FETCH_ASSOC);
		if ($result === false) {
			$totalRecords = 0;
		} else {
			$totalRecords = (int)$result['total'];
		}

        $totalPages = ceil($totalRecords / $perPage);

        // Get paginated results
        $offset = ($page - 1) * $perPage;
        $query = "SELECT DISTINCT r.* " . $baseQuery . " " . $whereClause . " 
                 ORDER BY r.uploaded_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        $demoList = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $demo) {
            $recordId = (int)$demo['record_id'];
            $demoList[$recordId] = $demo;
            $demoList[$recordId]['players'] = [];
        }

        // Get players for demos
        if (!empty($demoList)) {
            $playerStmt = $db->prepare(
                "SELECT * FROM `record_player` WHERE `record_id` IN (" . 
                implode(',', array_keys($demoList)) . ")"
            );
            $playerStmt->execute();

            foreach ($playerStmt->fetchAll(PDO::FETCH_ASSOC) as $player) {
                $demoList[(int)$player['record_id']]['players'][$player['account_id']] = $player;
            }
        }

        return $this->template('demo/index', [
            'secondaryTitle' => 'Demo index',
            'demoList' => $demoList,
            'playerId' => $playerId,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'perPage' => $perPage,
            'dateSearch' => $dateSearch,
            'allowedPerPage' => self::ALLOWED_PER_PAGE
        ]);
    }

    /**
     * @throws PrintableException
     */
    public function actionCleanup(): string
    {
        $app = $this->app();

        $hash = $this->getFromRequest('hash');
        $validHashes = [$app->dataRegistry()['cleanupRunHash'],
            $this->app()->config()['system']['upgradeKey']];

        if (!in_array($hash, $validHashes))
        {
            throw $this->exception('Not found', 404);
        }
        @set_time_limit(0);

        return $this->json([
            'success' => true,
            'entries' => \App\Util\Demo::cleanup($app)
        ]);
    }

    public function actionDelete(): string
    {
        $this->assertIsAdmin();
        $demoId = $this->getFromRequest('id');

        $stmt = $this->app()->db()->prepare('SELECT * FROM `record` WHERE `demo_id` = ?');
        $stmt->execute([$demoId]);
        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$record)
        {
            return $this->json([
                'success' => false
            ]);
        }

        return $this->json([
            'success' => \App\Util\Demo::delete($record)
        ]);
    }
}
