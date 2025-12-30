<?php

namespace src\support;

class IsWrong
{
    /**
     * @param string $index
     * @param string $value
     */
    public static function set(string $index, string $value): void
    {
        $_SESSION['isWrong'][$index] = $value;
    }

    /**
     * @param string $index
     */
    public static function get(string $index): mixed
    {
        if (isset($_SESSION['isWrong'][$index])) {
            $value = $_SESSION['isWrong'][$index];
            unset($_SESSION['isWrong'][$index]);

            return $value;
        }

        return null;
    }
}
