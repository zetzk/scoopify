<?php

namespace src\routes;

use src\app\controllers\SessionController;
use src\app\controllers\SkopeController;
use src\core\Route;


Route::get('/', SkopeController::class, 'index')
    ->name('skopes.index');

Route::get('/session/start/{uuid}', SessionController::class, 'start')
    ->name('session.start');

Route::get('/session/join/{uuid}', SessionController::class, 'join')
    ->name('session.join');

    Route::get('/session/close/{uuid}', SessionController::class, 'close')
    ->name('session.close');

// API to get participants in session
Route::get('/session/{id}/participants', SessionController::class, 'participants')
    ->name('session.participants');