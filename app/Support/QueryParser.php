<?php

namespace App\Support;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class QueryParser
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param int $maxLimitPage
     * @return array{0: int, 1: int}
     */
    public static function parsePage(Request $request, int $maxLimitPage = 100): array
    {
        $details = [];
        $limitPage = self::pageNumber($request, "limitPage", 10, $maxLimitPage, $details);
        $currentPage = self::pageNumber($request, "currentPage", 1, null, $details);

        if ($details !== []) {
            throw new HttpResponseException(response()->json([ "detail" => $details, ], 422));
        }

        return [ $currentPage, $limitPage, ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $field
     * @param int $default
     * @param int|null $max
     * @param array<int, array<string, mixed>> $details
     * @return int
     */
    protected static function pageNumber(Request $request, string $field, int $default, ?int $max, array &$details): int
    {
        $raw = $request->query($field);

        if ($raw === null || $raw === "") {
            return $default;
        }

        $text = is_scalar($raw) ? trim((string) $raw) : "";

        if (preg_match("/^[+-]?\d+$/", $text) !== 1) {
            $details[] = [
                "type" => "int_parsing",
                "loc" => [ "query", $field, ],
                "msg" => "Input should be a valid integer, unable to parse string as an integer",
                "input" => $raw,
                "ctx" => new \stdClass(),
            ];

            return $default;
        }

        $value = (int) $text;

        if ($value < 1) {
            $details[] = [
                "type" => "greater_than_equal",
                "loc" => [ "query", $field, ],
                "msg" => "Input should be greater than or equal to 1",
                "input" => $raw,
                "ctx" => [ "ge" => 1, ],
            ];
        } elseif ($max !== null && $value > $max) {
            $details[] = [
                "type" => "less_than_equal",
                "loc" => [ "query", $field, ],
                "msg" => "Input should be less than or equal to {$max}",
                "input" => $raw,
                "ctx" => [ "le" => $max, ],
            ];
        }

        return $value;
    }

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

            if ($field === "") {
                continue;
            }

            $parsed[] = [
                "field" => $field,
                "direction" => strtolower(trim($parts[1])) === "desc" ? "desc" : "asc",
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
