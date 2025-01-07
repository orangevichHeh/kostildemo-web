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
                        <a href="?page=<?= $currentPage - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="pagination-previous">Previous</a>
                    <?php endif; ?>
                    
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                           class="pagination-next">Next</a>
                    <?php endif; ?>

                    <ul class="pagination-list">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li>
                                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                                   class="pagination-link <?= $i === $currentPage ? 'is-current' : '' ?>" 
                                   aria-label="Page <?= $i ?>" 
                                   aria-current="<?= $i === $currentPage ? 'page' : 'false' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>