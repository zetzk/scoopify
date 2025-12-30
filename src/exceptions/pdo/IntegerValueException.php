<?php

namespace src\exceptions\pdo;

use Exception;
use src\traits\LogException;

class IntegerValueException extends Exception
{
    private string $entity = 'pdo';

    use LogException;

    function __construct(array $content = [])
    {
        $message = 'O valor deve ser um inteiro';
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
