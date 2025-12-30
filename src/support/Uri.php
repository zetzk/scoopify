<?php

namespace src\support;

class Uri
{
    /**
     * @return string
     */
    public static function get(): string
    {
        /** @var string */
        $urlParsed = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = array_filter(explode('/', trim($urlParsed)));
        $idx = array_search($_ENV['APP_DIR'], $uri);

        for ($i = 1; $i <= (int) $idx; $i++) {
            unset($uri[$i]);
        }

        return $uri = '/' . implode('/', $uri);
    }
}
