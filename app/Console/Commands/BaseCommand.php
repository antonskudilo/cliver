<?php

namespace App\Console\Commands;

use Cliver\Core\Console\Commands\CommandInterface;

abstract readonly class BaseCommand implements CommandInterface
{
    /**
     * @param array<int, string> $args
     * @return array<string, string|string[]|true>
     */
    protected function parseFilters(array $args): array
    {
        $filters = [];

        foreach ($args as $arg) {
            if (!str_starts_with($arg, '--')) {
                continue;
            }

            $pair = explode('=', substr($arg, 2), 2);

            $key = $pair[0];
            $value = $pair[1] ?? true; // if no value is provided — treat as a flag

            if (is_string($value) && str_contains($value, ',')) {
                $value = explode(',', $value);
            }

            $filters[$key] = $value;
        }

        return $filters;
    }
}
