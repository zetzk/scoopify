<?php

namespace src\exceptions\pdo;

use Exception;
use src\traits\LogException;

class ColumnDoesntHaveADefaultValueException extends Exception
{
    private string $entity = 'pdo';

    use LogException;

    function __construct(array $content = [])
    {
        $message = 'Coluna com valor pendente';
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
