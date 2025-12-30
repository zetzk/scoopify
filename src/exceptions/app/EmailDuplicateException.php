<?php

namespace src\exceptions\app;

use Exception;
use src\traits\LogException;

class EmailDuplicateException extends Exception
{
    private string $entity = 'app';

    use LogException;

    function __construct(array $content = [])
    {
        $message = 'E-mail fornecido já está em uso';
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
