<?php

namespace src\core;

use Exception;
use src\support\RedirectBack;
use src\support\Sessions;
use src\support\Uri;
use src\support\View;

class Bootstrap
{

    public static function start(): void
    {
        try {
            $r = (new Router)->get();
            if (!$r)
                throw new Exception("route.unavailable", 500);
            Middleware::execute($r['middlewares']);
            (new Controller(explode(":", implode('@', $r['action']))[0]));
        } catch (Exception $e) {
            if (!property_exists($e, 'entity')) {
                $message = $e->getMessage();
                $r = View::render('templates.error', ['mssg' => $message, 'code' => $e->getCode()]);
                echo $r::$isString;
                die;
            }

            Sessions::set("old", array_merge($_POST, $_GET), true);
            notification()->error($e->getMessage());
            redirect()->back()->make();
        }
    }
}
