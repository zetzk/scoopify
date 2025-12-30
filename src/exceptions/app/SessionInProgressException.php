<?php

namespace src\exceptions\app;

use Exception;
use src\traits\LogException;

class SessionInProgressException extends Exception
{
    private string $entity = 'app';

    use LogException;

    function __construct(array $content = [])
    {
        $message = "Sorry, this session is in progress. Wait for the end to start a new session.";
        $code = 500;
        $this->log($message, $code, json_encode($content));
        return parent::__construct($message, $code);
    }
}
