<?php

namespace src\support;

use Exception;
use src\core\Route;

class Redirect
{

    public string $uri;
    public mixed $returnClass;


    function back()
    {
        $this->returnClass = new RedirectBack();
        $this->uri = $this->returnClass->uri;
        return $this;
    }

    function uri(string $uri)
    {
        $this->returnClass = new RedirectUri($uri);
        $this->uri = $this->returnClass->uri;
        return $this;
    }

    function route(string $name, array $indexes = [], array $queryString = [])
    {
        $queryString = http_build_query($queryString);
        $this->returnClass = new RedirectRoute($name, $indexes);
        $this->uri = $this->returnClass->uri . '?' . $queryString;
        return $this;
    }

    function withSuccess(array|string $messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                notification()->success($message);
            }
        } else {
            notification()->success($messages);
        }
        return $this;
    }

    function withError(array|string $messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                notification()->error($message);
            }
        } else {
            notification()->error($messages);
        }
        return $this;
    }

    function withWarning(array|string $messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                notification()->warning($message);
            }
        } else {
            notification()->warning($messages);
        }
        return $this;
    }

    function withInfo(array|string $messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                notification()->info($message);
            }
        } else {
            notification()->info($messages);
        }
        return $this;
    }

    function make()
    {
        header('Location: ' . $this->uri);
    }
}




class RedirectUri
{
    public ?string $uri;
    function __construct(string $uri)
    {
        $this->uri = $uri;
    }
}




class RedirectBack
{
    public ?string $uri;
    function __construct()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->uri = $_SERVER['HTTP_REFERER'];
        } else
            $this->uri = '';
    }
}


class RedirectRoute
{
    public ?string $uri;
    function __construct(string $name, array $indexes)
    {
        $r = findUriByName(Route::routes(), $name);
        if ($r) {

            if (substr_count($r['uri'], '{') !== count($indexes))
                throw new Exception('route.missing.parameters ' . $name, 500);

            if (str_contains($r['uri'], '{')) {
                foreach ($indexes as $key => $index) {
                    $r['uri'] = str_replace('{' . $key . '}', $index, $r['uri']);
                }
            }
        }

        if (!$r)
            throw new Exception('Não foi possível encontrar uma rota com o nome: ' . $name);
        $this->uri = $r['uri'];
    }
}
