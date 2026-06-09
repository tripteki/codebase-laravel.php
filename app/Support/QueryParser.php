<?php

namespace App\Support;

class QueryParser
{
    /**
     * @param string|null $orders
     * @return array<int, array{field: string, direction: string}>
     */
    public static function parseOrders(?string $orders): array
    {
        if ($orders === null || $orders === "") {
            return [];
        }

        $parsed = [];

        foreach (explode(",", $orders) as $order) {
            $parts = explode(":", $order, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $field = trim($parts[0]);
            $direction = strtolower(trim($parts[1]));

            if ($field === "" || ! in_array($direction, [ "asc", "desc", ], true)) {
                continue;
            }

            $parsed[] = [
                "field" => $field,
                "direction" => $direction,
            ];
        }

        return $parsed;
    }

    /**
     * @param string|null $filters
     * @return array<int, array{field: string, search: string}>
     */
    public static function parseFilters(?string $filters): array
    {
        if ($filters === null || $filters === "") {
            return [];
        }

        $parsed = [];

        foreach (explode(",", $filters) as $filter) {
            $parts = explode(":", $filter, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $field = trim($parts[0]);
            $search = trim($parts[1]);

            if ($field === "" || $search === "") {
                continue;
            }

            $parsed[] = [
                "field" => $field,
                "search" => $search,
            ];
        }

        return $parsed;
    }
}
