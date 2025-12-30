<?php

namespace src\core;



class Middleware
{
    public static function execute(array $middlewares)
    {
        foreach ($middlewares as $m) {
            new $m();
        }
    }
}
