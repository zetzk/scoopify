<?php


use src\support\Redirect;

function redirect()
{
    return new Redirect();
}

function backError(array|string $messages): Redirect
{
    return (new Redirect())->back()->withError($messages);
}

function backSuccess(array|string $messages): Redirect
{
    return (new Redirect())->back()->withSuccess($messages);
}