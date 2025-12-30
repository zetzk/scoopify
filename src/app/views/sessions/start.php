<?php
$this->layout("templates/base", [
    "webTitle" => "Scopify - Start Session",
    "cardTitle" => "Scopify - Start Session",
    "styles" => [
        path()->css("sessions/start.css"),
    ],
    "js" => [],
]);

?>



<div>
    <h1>Start Session</h1>

    <p class="session-project">
        <span class="name"><?= $skope->title; ?></span>
    </p>
</div>


<div class="session-status">
    <div class="status-icon">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="white"></path>
        </svg>
    </div>
    <div class="status-text">
        <strong>1</strong> joined - <strong>4</strong> pending
    </div>
</div>


<div class="user-list">

    <div class="user-item selected">
        <img src="https://i.pravatar.cc/150?img=12" alt="Luiza Santos" class="user-avatar">
        <div class="user-info">
            <div class="user-name">Gabriel Ferreira</div>
            <div class="user-email">Entrou há 20 segundos</div>
        </div>
        <div class="user-action">
            <div class="check-icon">
                <i class="ph ph-check"></i>
            </div>
        </div>
    </div>

</div>

<button class="btn btn-company mt-3 btn-start">Start Session 🚀</button>