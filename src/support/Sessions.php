<?php

namespace src\support;


class Sessions
{

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $override
     * @return mixed
     */
    static function set(string $key, mixed $value, bool $override = false): mixed
    {
        if (!isset($_SESSION[$key]) or $override) {
            $_SESSION[$key] = $value;
            return $_SESSION[$key];
        }
        return null;
    }


    /**
     * @param string $key
     * @return mixed
     */
    static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string $key
     * @return void
     */
    static function unset(string $key): void
    {
        if (isset($_SESSION[$key]))
            unset($_SESSION[$key]);
    }
}
