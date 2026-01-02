<?php
$count = count($skopes);
$time = time();
$this->layout("templates/base", [
    "webTitle" => "scoopify - List ($count)",
    "cardTitle" => "scoopify - List ($count)",
    "styles" => [
        path()->css("skopes/index.css?v=$time"),
    ],
    "js" => [],
]);
?>



<div class="container-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Title</th>
                <th>Analyst</th>
                <th>Developer</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($skopes as $skope_idx => $skope) { ?>
                <tr>
                    <td><span><a href="#" class="t-gray">#SR<?= $skope->id ?></a></span></td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="status">
                                <div class="dot <?= $skope->is_estimated() ? 'estimado' : 'aguardando' ?>"></div>
                                <span><?= $skope->is_estimated() ? 'estimado' : 'aguardando' ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?= $skope->title ?></td>
                    <td><?= $skope->analyst ?></td>
                    <td><?= $skope->developer ?></td>
                    <td>

                        <?php if ($skope->in_session()) { ?>
                            <a href="#" class="btn btn-secondary">In progress <i
                                    class="ph ph-arrows-clockwise spinning"></i></a>
                        <?php } else if ($skope->waiting()) { ?>
                                <a href="<?= route("session.join", ["uuid" => $skope->uuid]) ?>" class="btn btn-company">Join in
                                    the session <i class="ph ph-rocket-launch"></i></a>
                        <?php } else if ($skope->is_estimated()) { ?>

                        <?php } else { ?>
                                    <a href="<?= route("session.start", ["uuid" => $skope->uuid]) ?>" class="btn btn-company">Start
                                        Session <i class="ph ph-arrow-right"></i></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<hr class="mb-5">


<?php if (!$has_active_session) { ?>
    <?php $isChristmasSeason = ((int) date('m') === 12 && (int) date('d') < 26); ?>
    <div id="session" class="d-flex flex-column align-items-center justify-content-center flex-direction-column">
        <h4 class="fw-bold">SEM SESSÃO ATIVA 🙌🏼</h4>
        <?php if ($isChristmasSeason): ?>
            <img src="<?= path()->images("xmas.gif") ?>" alt="Christmas">
        <?php else: ?>
            <img src="<?= path()->images("404.svg") ?>" alt="404 not found">
        <?php endif; ?>
    </div>
<?php } else { ?>
    <div id="session" class="d-flex flex-column align-items-center justify-content-center flex-direction-column">
        <h4 class="fw-bold">SESSÃO EM ANDAMENTO 🔄</h4>
        <img src="<?= path()->images("work-in-progress.svg") ?>" alt="404 not found">
    </div>
<?php } ?>