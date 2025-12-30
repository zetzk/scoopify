<?php

namespace src\core;


use src\support\RequestType;
use src\support\Uri;

class Router
{

    private string $uri;
    private string $method;
    private array $routesRegistered;

    public function __construct()
    {
        require __DIR__ . '/../routes/routes.php';
        $this->uri = Uri::get();
        $this->method = RequestType::get();
        $this->routesRegistered = Route::routes();
    }

    /**
     * Method for obtaining simple routes
     * 
     * @return array|null
     */
    private function simpleRouter(): array|null
    {
        return $this->routesRegistered[$this->method][$this->uri] ?? null;
    }


    /**
     * Method obtains dynamic routes
     * 
     * @return array|null
     */
    private function dynamicRouter(): array|null
    {
        $routerRegisteredFound = null;
        foreach ($this->routesRegistered[$this->method] as $index => $route) {
            if (isset($route['bind'])) {
                foreach ((array) $route['bind'] as $r) {
                    $index = str_replace('{' . $r['param'] . '}', $r['regex'], $index);
                }
            } else {
                $index = preg_replace('/\{.*?\}/', '[a-zA-Z0-9-_]+', $index);
            }
            $regex = str_replace('/', '\/', ltrim($index, '/'));
            if ($index !== '/' and preg_match("/^$regex$/", ltrim($this->uri, '/')))
                return (array) $route;
        }

        return $routerRegisteredFound;
    }


    /**
     * Processes the beginning to get simple or dynamic routes
     * 
     * @return array|null
     */
    public function get(): array|null
    {
        return $this->simpleRouter() ?: $this->dynamicRouter();
    }
}
