<?php

use src\support\View;

function view(string $view, array $data = []): View
{
    return View::render($view, $data);
}
