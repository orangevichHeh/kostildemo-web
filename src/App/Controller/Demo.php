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
    private const ITEMS_PER_PAGE = 10;

    public function actionIndex(): string
    {
        $db = $this->db();
        $page = max(1, (int)($this->getFromRequest('page') ?? 1));
        $search = $this->getFromRequest('search');
        $playerId = $this->getFromRequest('find');

        // Build base query
        $baseQuery = "FROM `record` r 
                     LEFT JOIN `record_player` rp ON r.record_id = rp.record_id";
        
        $whereConditions = [];
        $params = [];

        // Add search condition
        if ($search) {
            $whereConditions[] = "(r.map LIKE :search OR rp.username LIKE :search OR rp.account_id = :account_id)";
            $params[':search'] = "%$search%";
            // Try to convert search input to account_id if it's numeric
            $params[':account_id'] = is_numeric($search) ? (int)$search : -1;
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

        $totalPages = ceil($totalRecords / self::ITEMS_PER_PAGE);

        // Get paginated results
        $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        $query = "SELECT DISTINCT r.* " . $baseQuery . " " . $whereClause . " 
                 ORDER BY r.uploaded_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':limit', self::ITEMS_PER_PAGE, PDO::PARAM_INT);
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
            'search' => $search
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
