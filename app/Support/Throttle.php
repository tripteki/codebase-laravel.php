<?php

namespace App\Support;

class Throttle
{
    /**
     * @param list<string> $limits
     * @return list<string>
     */
    public static function middleware(string ...$limits): array
    {
        if (! app()->environment("production")) {
            return [];
        }

        return array_map(
            static fn (string $limit): string => "throttle:{$limit}",
            $limits,
        );
    }
}
