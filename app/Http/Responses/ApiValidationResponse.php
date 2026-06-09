<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use stdClass;

class ApiValidationResponse
{
    /**
     * @param \Illuminate\Validation\ValidationException $exception
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function fromException(ValidationException $exception, Request $request): JsonResponse
    {
        $payload = $exception->validator ? $exception->validator->getData() : [];
        $failed = $exception->validator ? $exception->validator->failed() : [];
        $errors = $exception->errors();
        $details = [];

        foreach ($failed as $field => $rules) {
            $messages = collect($errors[$field] ?? []);

            foreach ($rules as $rule => $parameters) {
                $ruleKey = strtolower((string) $rule);

                $details[] = [
                    "type" => self::resolveType($ruleKey),
                    "loc" => self::resolveLocation($request, (string) $field),
                    "msg" => self::resolveMessage((string) ($messages->shift() ?? "")),
                    "input" => data_get($payload, $field),
                    "ctx" => self::resolveContext($ruleKey, $parameters),
                ];
            }
        }

        return response()->json([ "detail" => $details, ], 422);
    }

    /**
     * @param string $rule
     * @return string
     */
    protected static function resolveType(string $rule): string
    {
        return match ($rule) {
            "required", "required_without", "required_with", "required_if", "present" => "missing",
            "email" => "value_error.email",
            "confirmed" => "value_error.confirmed",
            "unique" => "value_error.unique",
            "exists" => "value_error.not_found",
            "min" => "string_too_short",
            "max" => "string_too_long",
            "between" => "value_error.between",
            "size" => "value_error.size",
            "digits" => "value_error.digits",
            "uuid" => "value_error.uuid",
            "regex" => "value_error.regex",
            "same" => "value_error.same",
            "in" => "value_error.enum",
            "boolean" => "type_error.bool",
            "array" => "type_error.list",
            "file" => "value_error.file",
            "mimes" => "value_error.mime",
            "string" => "type_error.str",
            "integer", "numeric" => "type_error.number",
            default => "value_error",
        };
    }

    /**
     * @param string $rule
     * @param array<int|string, mixed> $parameters
     * @return object|array<string, mixed>
     */
    protected static function resolveContext(string $rule, array $parameters): object|array
    {
        $values = array_values($parameters);

        return match ($rule) {
            "min" => [ "min_length" => (int) ($values[0] ?? 0), ],
            "max" => [ "max_length" => (int) ($values[0] ?? 0), ],
            "between" => [
                "min" => (int) ($values[0] ?? 0),
                "max" => (int) ($values[1] ?? 0),
            ],
            "size" => [ "size" => (int) ($values[0] ?? 0), ],
            "digits" => [ "digits" => (int) ($values[0] ?? 0), ],
            "in" => [ "expected" => $values, ],
            "mimes" => [ "mimes" => $values, ],
            "same" => [ "field" => (string) ($values[0] ?? ""), ],
            "unique", "exists" => new stdClass(),
            default => $values === []
                ? new stdClass()
                : [ "error" => self::resolveMessage(implode(", ", array_map("strval", $values))), ],
        };
    }

    /**
     * @param string $message
     * @return string
     */
    protected static function resolveMessage(string $message): string
    {
        if ($message !== "" && str_starts_with($message, "_") && str_contains($message, ".")) {
            $translated = __($message);

            if ($translated !== $message) {
                return $translated;
            }
        }

        return $message;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $field
     * @return array<int, string>
     */
    protected static function resolveLocation(Request $request, string $field): array
    {
        $routeParameters = $request->route()?->parameters() ?? [];

        if (array_key_exists($field, $routeParameters)) {
            return [ "path", $field, ];
        }

        if ($request->isMethod("GET") || $request->isMethod("DELETE")) {
            if ($request->query->has($field)) {
                return [ "query", $field, ];
            }

            return [ "query", $field, ];
        }

        if (str_contains($field, ".")) {
            return array_merge([ "body", ], explode(".", $field));
        }

        return [ "body", $field, ];
    }
}
