<?php $t = time(); ?>
<!doctype html>
<html lang="pt-BR">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $webTitle ?? 'Web Title Default' ?></title>
    <link rel="stylesheet" href="<?= path()->css("/bs5.css?t={$t}") ?>">
    <link rel="stylesheet" href="<?= path()->css("/app.css?t={$t}") ?>">
    <link rel="stylesheet" href="<?= path()->css("/notification.css?t={$t}") ?>">
    <link rel="stylesheet" href="<?= path()->css("/sidebar.css?t={$t}") ?>">
    <link rel="stylesheet" href="<?= path()->css("/table-responsive.css?t={$t}") ?>">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <?= $this->insert('templates/styles', ['styles' => $styles ?? []]) ?>
</head>


<body class="<?= path()->get_company(); ?>" style="min-height: calc(100vh - 100px);">

    <div class="container mt-4" style="min-height: calc(100vh - 100px);">
        <ul class="notificationsToasts"></ul>
        <div class="card mb-4" style="min-height: inherit;">
            <div class="card-header">
                <div class="name-and-logo">
                    <span class="d-flex align-items-center">
                        <?php if (isset($backTo)) { ?><a href="<?= $backTo ?>"
                                class="d-flex text-decoration-none text-dark me-3"><i
                                    class="ph ph-arrow-left"></i></a><?php } ?><?= $cardTitle ?? null ?></span>
                    <img src="<?= path()->images("/ammx.png"); ?>" alt="Logo Empresa">
                </div>
            </div>
            <div class="card-body">
                <?= $this->section("content"); ?>
            </div>
        </div>
    </div>


    <?= $this->insert('templates/loading'); ?>

    <script src="<?= path()->js("/bs5.js?t={$t}") ?>"></script>
    <script src="<?= path()->js("/init.js?t={$t}"); ?>"></script>
    <script src="<?= path()->js("/notification.js?t={$t}"); ?>"></script>
    <?= $this->insert('templates/js', ['js' => $js ?? []]) ?>
    <?php enableNotifications(); ?>
    <?php forgetSessions(['old', 'zarkify', 'isWrong']); ?>
</body>

</html>