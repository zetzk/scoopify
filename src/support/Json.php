<?php
namespace src\support;




final class Json
{
    public static string|bool $isString;


    /**
     * This method is responsible for configuring the isString variable with JSON
     * 
     * @param object|array<mixed, mixed> $data
     * @return self
     */
    public static function return(object|array $data, int $statusCode): self
    {
        http_response_code($statusCode);
        self::$isString = json_encode($data);
        return new static;
    }
}
