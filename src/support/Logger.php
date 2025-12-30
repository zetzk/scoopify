<?php

namespace src\support;

use src\interfaces\LoggerInterface;

class Logger
{
    function info(string $message): void
    {
        dump($message);
    }
}
