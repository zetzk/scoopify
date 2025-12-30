<?php

namespace src\core;

use DI\Container;
use Exception;
use ReflectionClass;
use src\support\Json;
use src\support\Redirect;
use src\support\RedirectBack;
use src\support\RedirectRoute;
use src\support\RedirectUri;
use src\support\RequestType;
use src\support\Uri;
use src\support\View;

use function DI\get;

class Controller
{


    public function __construct(string $router)
    {
        [$controller, $method] = $this->validateRoute($router);
        $this->validateController($controller);
        $this->validateMethod($controller, $method);
        $this->execute($router, $controller, $method);
    }


    public function execute(string $router, string $controller, string $method): void
    {
        $params = $this->getParamsInRoute($router);
        /** @var Container */
        $container = $this->startContainerInjection();
        $controllerObject = $container->get($controller);

        /** @var Redirect|View|Json|null */
        $response = $this->handleRequest($controller, $method, $controllerObject, $container, $params);
        if (!$response)
            throw new Exception("Controlller's return content empty: {$controller}", 500);


        if (in_array(true, [$response instanceof View, $response instanceof Json])) {
            echo $response::$isString;
        } else {
            if (in_array(true, [$response->returnClass instanceof RedirectUri, $response->returnClass instanceof RedirectRoute])) {
                $url = path()->get_url();
                header("Location: {$url}{$response->uri}");
            } else if ($response->returnClass instanceof RedirectBack) {
                header('Location: ' . $response->uri);
            }
        }
    }

    /**
     * @param string $router
     * @return array<int, string>
     */
    private function validateRoute(string $router): array
    {
        if (substr_count($router, '@') <= 0)
            throw new Exception("route.wrong", 500);

        return explode('@', $router);
    }

    private function validateController(string $controller): void
    {
        if (!class_exists($controller))
            throw new Exception("controller.unavailable ({$controller})", 500);
    }

    /**
     * @param string $controller
     * @param string $method
     */
    private function validateMethod(string $controller, string $method): void
    {
        if (!method_exists($controller, $method)) {
            throw new Exception("method.unavailable ({$controller}:{$method})", 500);
        }
    }

    private function startContainerInjection(): object
    {
        $container = require_once(__DIR__ . "/container.php");
        return $container;
    }

    /**
     * @param string $router_
     * @return array<int, mixed>|bool
     */
    private function getParamsInRoute(string $router_): array|bool
    {
        $uri = Uri::get();
        $requestMethod = RequestType::get();
        $routes = Route::routes();
        $routesClean = $this->clearRoutesWithoutMiddlewares($routes, $requestMethod);


        $router = array_search($router_, $routesClean[$requestMethod]);
        if ($router) {

            $explodeUri = array_values(array_filter(explode('/', $uri)));
            $explodeRouter = array_values(array_filter(explode('/', $router)));


            $params = [];
            foreach ($explodeRouter as $index => $routerSegment) {
                if (
                    isset($explodeUri[$index])
                    && $routerSegment !== $explodeUri[$index]
                )
                    $params[$index] = $explodeUri[$index];
            }


            return $params;
        }

        return false;
    }

    /**
     * @param string $controller
     * @param string $method
     * @param object $controllerObject
     * @param Container $container
     * @param array<int, mixed>|bool $params
     */
    private function handleRequest(string $controller, string $method, object $controllerObject, $container, array|bool $params): ?object
    {

        if (class_exists($controller)) {
            $reflection = new ReflectionClass($controller);
            $parameters = $reflection->getMethod($method)->getParameters();

            $params = $params ?: [];
            $paramsSpecials = [];


            if ($parameters) {
                foreach ($parameters as $parameterFromMethod) {
                    $parameterNameAbsolute = $parameterFromMethod->getType()?->getName();
                    if (!in_array($parameterNameAbsolute, ["string", "int", "bool", "float"])) {
                        $r = (new $parameterNameAbsolute);
                        $r->execute();
                        $paramsSpecials[] = (new $parameterNameAbsolute);
                    }
                }
                $response = $controllerObject->$method(...$paramsSpecials, ...$params);
            } else
                $response = $controllerObject->$method(...$params);

            return $response;
        }
        return null;
    }


    /**
     * @param array<string, array<string, string>> $routes
     * @param string $requestMethod
     * @return array<string, array<string, string>>
     */
    private function clearRoutesWithoutMiddlewares(array $routes, string $requestMethod): array
    {

        foreach ($routes[$requestMethod] as $routeKey => $routeValue) {
            $routes[$requestMethod][$routeKey] = implode('@', (array) $routeValue['action']);
        }
        return $routes;
    }
}
