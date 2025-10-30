<?php

if (!function_exists('get_value_type')) {
    /**
     * @param mixed $value
     * @return string
     * @throws ReflectionException
     */
    function get_value_type(mixed $value): string
    {
        if (is_object($value)) {
            return (new ReflectionClass($value))->getShortName();
        }

        return gettype($value);
    }
}

if (!function_exists('array_first')) {
    /**
     * @param array $array
     * @param mixed|null $default
     * @return mixed
     */
    function array_first(array $array, mixed $default = null): mixed
    {
        if (empty($array)) {
            return $default;
        }

        return $array[0];
    }
}

if (!function_exists('config')) {
    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function config(string $key, mixed $default = null): mixed
    {
        // Cache for loaded configs
        static $configs = null;

        if ($configs === null) {
            $configs = [];

            foreach (glob(config_path('/*.php')) as $file) {
                $name = basename($file, '.php');
                $configs[$name] = require $file;
            }
        }

        // Split the key: first part = file name, the rest = path inside config
        $parts = explode('.', $key, 2);
        $file  = $parts[0];
        $path  = $parts[1] ?? null;

        if (!isset($configs[$file])) {
            return $default;
        }

        $value = $configs[$file];

        if (isset($path)) {
            foreach (explode('.', $path) as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    return $default;
                }

                $value = $value[$segment];
            }
        }

        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * @param string $path
     * @return string
     */
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

if (!function_exists('csv_path')) {
    /**
     * @param string $filename
     * @return string
     */
    function csv_path(string $filename = ''): string
    {
        $base = config('datasource.csv.path');

        return rtrim($base, DIRECTORY_SEPARATOR) . ($filename ? DIRECTORY_SEPARATOR . $filename : '');
    }
}
