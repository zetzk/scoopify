<?php

namespace src\support;



use Exception;
use League\Plates\Engine;

final class View
{

    public static string $isString;
    public static string $redirect;

    /**
     * @param string $view
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = []): self
    {
        $viewHandled = str_replace(".", "/", $view);
        self::certifyIfViewExist($viewHandled);

        $platesEngine = new Engine('./src/app/views');
        $platesEngine->addData($data);
        self::$isString = $platesEngine->render(
            $viewHandled
        );

        return new static;
    }


    /**
     * @param string $view
     */
    private static function certifyIfViewExist(string $view): void
    {
        $viewPath = "./src/app/views/{$view}.php";
        if (!file_exists($viewPath))
            throw new Exception("view.unavailable " . $viewPath, 500);
    }
}
