<?php

/**
 * Its responsible for return only indexes selecteds
 *
 * @param array<mixed, mixed> $array
 * @param array<int, mixed> $keys
 * @return array<mixed, mixed>
 */
function array_only(array $array, array $keys): array
{
    $return = [];
    foreach ($keys as $key) {
        $return[$key] = $array[$key];
    }
    return $return;
}


/**
 * Its responsible for return only one index from array
 *
 * @param array<mixed, mixed> $array
 * @param string $key
 * @return array<mixed, mixed>
 */
function array_only_one(array $array, string $key): array
{
    return $array[$key] ?: [];
}


/**
 * Its responsible for return all indexes, excepts that will passed by parameter
 *
 * @param array<mixed, mixed> $array
 * @param array<int, mixed> $keys
 * @return array<mixed, mixed>
 */
function array_excepts(array $array, array $keys): array
{
    $return = [];
    foreach ($array as $key => $value) {
        if (!in_array($key, $keys)) $return[$key] = $array[$key];
    }
    return $return;
}




/**
 * fill in the values with null where they have empty strings
 * @param array<mixed, mixed> $array
 * @return array<mixed, mixed>
 */
function array_fill_empty_to_null(array $array): array
{
    foreach ($array as $key => &$value) {
        $value = ($value === '') ? null : $value;
    }
    return $array;
}
