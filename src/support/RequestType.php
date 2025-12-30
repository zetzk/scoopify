<?php

namespace src\support;


class RequestType
{
    /**
     * @return string
     */
    public static function get(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}
