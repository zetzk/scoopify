<?php

/**
 * Responsible for adding messages to the "Zarkify" array for viewing on view
 * @param string $message - The Message to display
 * @param string $type - The type of message
 * @return void
 */
function show_notification(string $message, string $type, string $local = "top-right", int $time = 5000): void
{
    $_SESSION["zarkify"][] = [
        "type" => $type,
        "message" => $message,
        "local" => $local,
        "time" => $time,
    ];
}


/**
 * Responsible display messages using js
 * @return void
 */
function enableNotifications(): void
{
    if (isset($_SESSION["zarkify"])) {
        foreach ($_SESSION["zarkify"] as $toastKey => $toast) {
            $message = str_replace("'", "\'", $toast['message']);
            $type = $toast['type'];
            $local = $toast["local"];
            $timeRemove = $toast["time"];
            $time = $toastKey * 1000;
            echo "<script>
                setTimeout(() => notificationsToast('{$type}', '{$message}', '{$local}', {$timeRemove}), $time)
            </script>";
        }
        unset($_SESSION["zarkify"]);
    }
}



function notification()
{
    return new class {
        public function success(string $m)
        {
            show_notification($m, 'success');
            return $this;
        }

        public function error(string $m)
        {
            if (str_contains($m, 'route.')) 
                throw new Exception($m, 500);
            show_notification($m, 'error');
            return $this;
        }

        public function warning(string $m)
        {
            show_notification($m, 'warning');
            return $this;
        }

        public function info(string $m)
        {
            show_notification($m, 'info');
            return $this;
        }
    };
}
