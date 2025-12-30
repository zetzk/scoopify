<?php

namespace src\exceptions\app;

use Exception;
use src\traits\LogException;

class NotFoundSessionException extends Exception
{
    private string $entity = 'app';

    use LogException;

    function __construct(array $content = [])
    {
        $message = 'Not found register. If continue, contact the administrator.';
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
