<?php
/**
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedVariableInspection
 * @noinspection PhpUnhandledExceptionInspection
 */
?><!DOCTYPE html>
<html class="demoSystem" data-public-url="<?= $this->publicUrl() ?>">
    <head>
        <?php
            $siteName = $options['siteName'];
            $pageTitle = isset($secondaryTitle) ? "$siteName | $secondaryTitle" : $siteName;
        ?>
        <title><?= $pageTitle ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css">
        <script type="module" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule="" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
        <link rel="stylesheet" href="assets/css/main.css" />
        <link rel="stylesheet" href="assets/css/theme.css" />
        <script src="assets/js/public.js"></script>

        <?php foreach ($headAdditionalCode ?? [] as $key => $str): ?>
            <!-- <?= $key ?> -->
            <?= $str ?>
            <!-- /<?= $key ?> -->
        <?php endforeach; ?>
    </head>
    <body>
        <div class="main-content">
            <section class="section">
                <div class="container">
                    <div class="columns">
                        <div class="column">
                            <h1 class="title"><?= $secondaryTitle ?? $pageTitle ?></h1>
                        </div>
                        <div class="column">
                            <div class="field has-addons">
                                <div class="control has-icons-left is-expanded">
                                    <input class="input" type="text" id="searchInput" 
                                           placeholder="Map name, player name, account ID or date (DD.MM)...">
                                    <span class="icon is-left">
                                        <ion-icon name="search-outline"></ion-icon>
                                    </span>
                                </div>
                                <div class="control">
                                    <div class="select">
                                        <select id="perPageSelect">
                                            <?php foreach ($allowedPerPage ?? [10, 25, 50] as $value): ?>
                                                <option value="<?= $value ?>" <?= ($perPage ?? 10) == $value ? 'selected' : '' ?>>
                                                    <?= $value ?> per page
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="control">
                                    <button class="button" id="themeToggle">
                                        <span class="icon">
                                            <ion-icon name="moon-outline" id="themeIcon"></ion-icon>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="section">
                <div class="container">
                    <?= $pageContent ?>
                </div>
            </section>
        </div>

        <footer class="footer">
            <div class="content has-text-centered">
                <p>
                    <strong>Demo System</strong> by <a href="https://github.com/Bubuni-Team" target="_blank" rel="noopener">Bubuni Team</a>. <br>
                    <strong>Fork</strong> by <a href="https://github.com/OrangevichHeh" target="_blank" rel="noopener">OrangevichHeh</a>. <br>
                    Наполнено силой Bulma и PHP
                </p>
            </div>
        </footer>
    </body>
</html>