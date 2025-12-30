<?php

namespace src\support;

use src\interfaces\NotificationInterface;

class Notification
{
    /**
     * @param string $message
     */
    function success(string $message): self
    {
        notification($message, "success");
        return $this;
    }

    /**
     * @param string $message
     */
    function info(string $message): self
    {
        notification($message, "info");
        return $this;
    }

    /**
     * @param string $message
     */
    function error(string $message): self
    {
        notification($message, "error");
        return $this;
    }

    /**
     * @param string $message
     */
    function warning(string $message): self
    {
        notification($message, "warning");
        return $this;
    }
}
