<?php

namespace src\traits;

trait LogException
{

    function log(string $message, int $code, string $content)
    {
        $dir = dirname(__DIR__) . "/logs/{$this->entity}/";
        if (is_dir($dir)) {
            $files = glob($dir . "*.log");
            $today = new \DateTime();
            $days = 2;

            foreach ($files as $file) {
                $filename = basename($file, ".log");
                $fileDate = \DateTime::createFromFormat('Y-m-d', $filename);

                if ($fileDate) {
                    $diff = $today->diff($fileDate);
                    if ($diff->days > $days) {
                        unlink($file);
                    }
                }
            }
        }


        if (!is_dir($dir))
            mkdir($dir, 0777, true);

        $filename = date('Y-m-d') . ".log";
        $logFile = $dir . $filename;
        $datetime = date('Y-m-d H:i:s');

        $nContent = " | Content: {$content}";
        $info = "Error: {$message} | Code: {$code} | When: {$datetime}{$nContent}";
        file_put_contents($logFile, $info . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
