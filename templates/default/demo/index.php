<section class="section">
    <div class="container">
        <?php if (empty($demoList)): ?>
            <div class="notification">
                Пока что здесь нет демо-записей
            </div>
        <?php else: ?>
            <?php foreach ($demoList as $demo): ?>
                <?= $this->renderTemplate('demo/entry', [
                    'demo' => $demo,
                    'server' => $this->config()['servers'][(int)$demo['server_id']],
                    'playerId' => $playerId
                ]); ?>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : ($playerId ? '&find=' . urlencode($playerId) : '') ?>" 
                           class="pagination-control pagination-previous">Previous</a>
                    <?php endif; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : ($playerId ? '&find=' . urlencode($playerId) : '') ?>" 
                           class="pagination-control pagination-next">Next</a>
                    <?php endif; ?>

                    <ul class="pagination-list">
                        <?php
                        $pagesToShow = [];
                        if ($totalPages <= 8) {
                            $pagesToShow = range(1, $totalPages);
                        } else {
                            $pagesToShow[] = 1;
                            
                            // Show pages around current page
                            for ($i = max(2, $currentPage - 2); $i <= min($currentPage + 2, $totalPages - 1); $i++) {
                                $pagesToShow[] = $i;
                            }
                            
                            $pagesToShow[] = $totalPages;
                        }
                        
                        // Add the page numbers with dots
                        $prevPage = 0;
                        foreach ($pagesToShow as $i):
                            if ($prevPage && $i - $prevPage > 1): ?>
                                <li><span class="pagination-ellipsis">&hellip;</span></li>
                            <?php endif; ?>
                            
                            <li>
                                <?php 
                                // Ensure consistent type comparison by converting both to integers
                                $isCurrentPage = (int)$i === (int)$currentPage;
                                ?>
                                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : ($playerId ? '&find=' . urlencode($playerId) : '') ?>" 
                                   class="pagination-link <?= $isCurrentPage ? 'is-current' : '' ?>"
                                   aria-label="Page <?= $i ?>" 
                                   aria-current="<?= $isCurrentPage ? 'page' : 'false' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php 
                            $prevPage = $i;
                        endforeach; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>