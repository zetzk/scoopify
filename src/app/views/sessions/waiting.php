<?php
$this->layout("templates/base", [
    "webTitle" => "Scopify - Start Session",
    "cardTitle" => "Scopify - Start Session",
    "styles" => [
        path()->css("sessions/waiting.css"),
    ],
    "js" => [
        path()->js("app/session/waiting.js"),
    ],
]);
?>


<!-- hidden info -->
<div class="hidden-info">
    <input type="text" id="skope_id" value="<?= $skope->id ?>">
    <input type="text" id="session_id" value="<?= $session->id ?>">
</div>


<!-- message init wrong -->
<div class="mb-3">
    <small class="d-flex justify-content-center align-items-center">Started the session by mistake? &nbsp; <a class="d-flex align-items-center justify-content-center g-3"><i class="ph ph-arrow-left me-1"></i> go back</a></small>
</div>

<!-- header/title -->
<div>
    <h1 class="mb-3">Waiting anothers participants</h1>

    <p class="session-project">
        <span class="name"><?= $skope->title; ?></span>
    </p>
</div>


<!-- status/contador -->
<div class="session-status">
    <div class="status-icon">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                fill="white"></path>
        </svg>
    </div>
    <div class="status-text" id="statusText">
        <span>Aguardando participantes...</span>
    </div>
</div>

<!-- lista de participantes -->
<div class="user-list">
</div>


<!-- loading -->
<div class="wait-loader mt-5">
    <p class="wait-heading">Loading</p>
    <div class="wait-loading">
        <div class="wait-load"></div>
        <div class="wait-load"></div>
        <div class="wait-load"></div>
        <div class="wait-load"></div>
    </div>
</div>



<!-- go to session (só é exibido para o host) -->
<?php if ($is_host) { ?>
    <div class="d-flex justify-content-center align-items-center flex-column mt-4">
        <small class="text-muted">Already? <a href="" class="">Go Now 🚀</a></small>
    </div>
<?php } ?>

