<?php

namespace src\support;

class Request
{
    
    /**
     * @param string $name
     * @return string|null|array<mixed, mixed>
     */
    public static function input(string $name): string|null|array
    {
        return $_POST[$name] ?? null;
    }


    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function setInput(string $key, mixed $value): mixed
    {
        $_POST[$key] = $value;
        return $_POST[$key];
    }


    /**
     * @return array<mixed, mixed>
     */
    public function get(): array
    {
        return $_POST;
    }

    /**
     * @return array<mixed, mixed>
     */
    public static function all(): array
    {
        return $_POST;
    }

    /**
     * @param string|array<int, string> $only
     * @return array<string, mixed>
     */
    public static function only(string|array $only): array
    {
        $fieldsPost = self::all();
        $fieldsPostKeys = array_keys($fieldsPost);

        $fieldsFiltered = [];
        foreach ($fieldsPostKeys as $index => $value) {
            $onlyField = (is_string($only) ? $only : (isset($only[$index]) ? $only[$index] : null));
            if (isset($fieldsPost[$onlyField]))
                $fieldsFiltered[$onlyField] = $fieldsPost[$onlyField];
        }

        return $fieldsFiltered;
    }

    /**
     * @param string|array<int, string> $excepts
     * @return array<mixed, mixed>
     */
    public static function excepts(string|array $excepts): array
    {
        $fieldsPost = self::all();

        if (is_array($excepts)) {
            foreach ($excepts as $index => $value) {
                if (isset($fieldsPost[$value]))
                    unset($fieldsPost[$value]);
            }
        } else if (is_string($excepts)) {
            if (isset($fieldsPost[$excepts]))
                unset($fieldsPost[$excepts]);
        }

        return $fieldsPost;
    }


    /**
     * @param ?string $param null - as default
     * @return string|array<string, int|string>|null
     */
    public static function getQuery(?string $param = null): string|array|null
    {
        if ($param)
            return self::query($param);
        return $_GET;
    }


    
    /**
     * @param ?string $param null - as default
     * @return string|array<string, int|string>|null
     */
    public static function query_params(?string $param = null): string|array|null
    {
        if ($param)
            return self::query($param);
        return $_GET;
    }


    /**
     * @param string $name
     * @return ?string
     */
    public static function query(string $name): ?string
    {
        if (!isset($_GET[$name]))
            return null;
        return $_GET[$name] != '' ? $_GET[$name] : null;
    }

    /**
     * @param array<mixed, mixed> $data
     * @return string|false
     */
    public static function toJson(array $data): string|false
    {
        return json_encode($data);
    }

    public static function countPost(string $input): int
    {
        return count($_POST[$input]);
    }
}
