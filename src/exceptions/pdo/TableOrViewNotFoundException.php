<?php

namespace src\exceptions\pdo;

use Exception;
use src\traits\LogException;

class TableOrViewNotFoundException extends Exception
{
    private string $entity = 'pdo';

    use LogException;

    function __construct(array $content = [])
    {
        $message = 'Tabela ou view inexistente';
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
