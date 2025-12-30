<?php

use src\core\Route;

/**
 * Its responsible for joining uri with base url from app
 *
 * @param string $route
 * @return string
 */
function route(string $routeName, array $indexes = [], array $query_params = [])
{

    $r = findUriByName(Route::routes(), $routeName);
    if ($r) {

        if (substr_count($r['uri'], '{') !== count($indexes)) {
            preg_match_all('/\{(.*?)\}/', $r['uri'], $matches);
            $keys = implode(', ', array_map(fn($item) => '{' . $item . '}', $matches[1]));
            throw new Exception('route.missing.parameters: ' . $keys, 500);
        }


        if (str_contains($r['uri'], '{')) {
            preg_match_all('/\{(.*?)\}/', $r['uri'], $matches);
            $keys = implode(', ', array_map(fn($item) => '{' . $item . '}', $matches[1]));

            foreach ($indexes as $key => $value) {
                if (!str_contains($r['uri'], '{' . $key . '}'))
                    throw new Exception("route.missing.parameters: {$keys} " . '<br> passed: {' . $key . '} not found', 500);
                $r['uri'] = str_replace('{' . $key . '}', $value, $r['uri']);
            }
        }
    }

    $url = path()->get_url();
    if (!$r)
        throw new Exception('route.404: ' . $routeName);
    if ($query_params)
        return $url . $r['uri'] . "?" . http_build_query($query_params);
    return $url . $r['uri'];
}


function findUriByName(array $routes, string $name)
{
    foreach ($routes as $method => $routeGroup) {
        foreach ($routeGroup as $uri => $route) {
            if (isset($route['name']) && $route['name'] === $name) {
                return ['uri' => $uri, 'info' => $route];
            }
        }
    }
    return null;
}
