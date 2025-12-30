<?php

namespace src\core;

use src\support\Uri;

class Route
{

    private static array $routes = ["get" => [], "post" => []];
    private static ?string $lastUri = null;


    public static function routes()
    {
        return self::$routes;
    }



    private static function add(string $method, string $uri, string $controller, string $controllerMethod, array $middlewares = []): self
    {
        if (strlen($uri) > 1)
            $uri = rtrim($uri, '/');
        self::$routes[strtolower($method)]["{$uri}"] = [
            "action" => ['controller' => $controller, "method" => $controllerMethod],
            "middlewares" => $middlewares,
            "name" => ''
        ];
        self::$lastUri = $uri;
        return new self;
    }


    /**
     * Responsible for adding a get type route
     * @param string $endpoint
     * @param string $controller
     * @param string $method
     * @param array<int, string> $middlewares
     */
    public static function get(string $endpoint, string $controller, string $method, array $middlewares = [])
    {
        return self::add('get', $endpoint, $controller, $method, $middlewares);
    }

    /**
     * Responsible for adding a post type route
     * @param string $endpoint
     * @param string $controller
     * @param string $method
     * @param array<int, string> $middlewares
     */
    public static function post(string $endpoint, string $controller, string $method, array $middlewares = []): self
    {
        return self::add('post', $endpoint, $controller, $method, $middlewares);
    }


    /**
     * Responsible for receiving and executing the meddlewares
     * 
     * @param array<int, string> $middlewares
     * @param string $prefix
     */
    public static function middlewares(array $middlewares = []): self
    {
        if (str_contains(self::$lastUri, '{')) {
            if (self::isCurrentRoute()) {
                foreach ($middlewares as $middlewareKey => $middleware) {
                    (new $middleware)->execute(self::$lastUri);
                }
            }
        } else {
            if (self::$lastUri === Uri::get()) {
                foreach ($middlewares as $middlewareKey => $middleware) {
                    (new $middleware)->execute(self::$lastUri);
                }
            }
        }
        return new self;
    }

    private static function isCurrentRoute(): bool
    {

        // Exemplo de valores

        $browserUrl = Uri::get(); // URL do navegador
        $routePattern = self::$lastUri; // Última rota registrada
        $routeData = self::$routes['get'][self::$lastUri];
        


        // Substituir os parâmetros na rota pelo regex correspondente
        $regexRoute = preg_replace_callback('/\{(.*?)\}/', function ($matches) use ($routeData) {
            foreach ($routeData['bind'] as $bind) {
                if ($bind['param'] === $matches[1]) {
                    return '(' . $bind['regex'] . ')';
                }
            }
            return $matches[0]; // Caso não encontre, mantém original
        }, $routePattern);

        // Criar regex completa para correspondência
        $regexRoute = '#^' . $regexRoute . '$#';

        // Verificar se a URL do navegador corresponde ao padrão da rota
        return preg_match($regexRoute, $browserUrl);
    }



    public static function name(string $name): self
    {
        $methodKeys = array_keys(self::$routes);
        foreach ($methodKeys as $method) {
            if (isset(self::$routes[$method][self::$lastUri])) {
                self::$routes[$method][self::$lastUri]['name'] = $name;
                break;
            }
        }
        return new self;
    }


    public static function whereInt(string|array $binds): self
    {
        $methodKeys = array_keys(self::$routes);
        foreach ($methodKeys as $method) {
            if (isset(self::$routes[$method][self::$lastUri])) {
                if (is_array($binds)) {
                    foreach ($binds as $key => $bind) {
                        self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $bind, 'regex' => '[0-9]+'];
                    }
                } else
                    self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $binds, 'regex' => '[0-9]+'];
                break;
            }
        }
        return new self;
    }


    public static function whereString(string|array $binds): self
    {
        $methodKeys = array_keys(self::$routes);
        foreach ($methodKeys as $method) {
            if (isset(self::$routes[$method][self::$lastUri])) {
                if (is_array($binds)) {
                    foreach ($binds as $key => $bind) {
                        self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $bind, 'regex' => '[a-zA-z0-9-_]+'];
                    }
                } else
                    self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $binds, 'regex' => '[a-zA-z0-9-_]+'];
                break;
            }
        }
        return new self;
    }

    public static function whereUuid(string|array $binds): self
    {
        $methodKeys = array_keys(self::$routes);
        foreach ($methodKeys as $method) {
            if (isset(self::$routes[$method][self::$lastUri])) {
                if (is_array($binds)) {
                    foreach ($binds as $key => $bind) {
                        self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $bind, 'regex' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'];
                    }
                } else
                    self::$routes[$method][self::$lastUri]['bind'][] = ['param' => $binds, 'regex' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'];
                break;
            }
        }
        return new self;
    }

    public static function where(array $data)
    {


        foreach ($data as $param => $type) {
            if ($type === 'int')
                self::whereInt($param);

            else if ($type === 'string')
                self::whereString($param);

            else if ($type === 'uuid')
                self::whereUuid($param);
        }
        return new self;
    }
}
