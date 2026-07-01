<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
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
                $message = self::resolveMessage((string) ($messages->shift() ?? ""));
                $input = self::resolveInput(data_get($payload, $field));

                if (self::isPasswordMismatch($message)) {
                    $details[] = self::mismatchDetail($message, $payload);

                    continue;
                }

                $shared = $ruleKey === strtolower(Enum::class)
                    ? self::enumConstraint($exception->validator->getRules()[$field] ?? [])
                    : self::sharedConstraint($ruleKey, $message, (string) $field, $parameters, $input);
                $missing = ($shared["type"] ?? self::resolveType($ruleKey)) === "missing";

                $details[] = [
                    "type" => $shared["type"] ?? self::resolveType($ruleKey),
                    "loc" => self::resolveLocation($request, (string) $field),
                    "msg" => $shared["msg"] ?? $message,
                    "input" => $missing ? (object) $payload : $input,
                    "ctx" => $shared["ctx"] ?? self::resolveContext($ruleKey, $parameters),
                ];
            }
        }

        if ($details === []) {
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $resolved = self::resolveMessage((string) $message);
                    $input = self::resolveInput(data_get($payload, $field));
                    $shared = self::sharedConstraint("", $resolved, (string) $field, [], $input);

                    if ($shared === null && self::isPasswordMismatch($resolved)) {
                        $details[] = self::mismatchDetail($resolved, $payload);

                        continue;
                    }

                    $details[] = [
                        "type" => $shared["type"] ?? "value_error",
                        "loc" => self::resolveLocation($request, (string) $field),
                        "msg" => $shared["msg"] ?? $resolved,
                        "input" => ($shared["type"] ?? "") === "missing" ? (object) $payload : $input,
                        "ctx" => $shared["ctx"] ?? new stdClass(),
                    ];
                }
            }
        }

        return response()->json([ "detail" => $details, ], 422);
    }

    /**
     * @param string $rule
     * @param string $message
     * @param string $field
     * @param array<int|string, mixed> $parameters
     * @param mixed $input
     * @return array{type: string, msg: string, ctx: array<string, mixed>}|null
     */
    protected static function sharedConstraint(string $rule, string $message, string $field, array $parameters, mixed $input): ?array
    {
        $attributes = array_values(array_unique([
            $field,
            str_replace("_", " ", $field),
            str_replace(".", " ", $field),
            str_replace("_", " ", Str::snake($field)),
        ]));

        if ($rule === "" || in_array($rule, [ "required", "present", ], true)) {
            foreach ($attributes as $attribute) {
                if (self::isDefaultMessage($message, "validation.required", [ "attribute" => $attribute, ])) {
                    return [
                        "type" => "missing",
                        "msg" => "Field required",
                        "ctx" => new stdClass(),
                    ];
                }
            }
        }

        if ($rule === "" || $rule === "min") {
            $length = self::matchingLength($message, "validation.min.string", $attributes, "min");

            if ($length !== null) {
                return [
                    "type" => "string_too_short",
                    "msg" => "String should have at least {$length} characters",
                    "ctx" => [ "min_length" => $length, ],
                ];
            }
        }

        if ($rule === "" || $rule === "max") {
            $length = self::matchingLength($message, "validation.max.string", $attributes, "max");

            if ($length !== null) {
                return [
                    "type" => "string_too_long",
                    "msg" => "String should have at most {$length} characters",
                    "ctx" => [ "max_length" => $length, ],
                ];
            }
        }

        if ($rule === "array") {
            foreach ($attributes as $attribute) {
                if (self::isDefaultMessage($message, "validation.array", [ "attribute" => $attribute, ])) {
                    return [
                        "type" => "list_type",
                        "msg" => "Input should be a valid list",
                        "ctx" => new stdClass(),
                    ];
                }
            }
        }

        if ($rule === "in" && $parameters !== []) {
            foreach ($attributes as $attribute) {
                if (! self::isDefaultMessage($message, "validation.in", [ "attribute" => $attribute, ])) {
                    continue;
                }

                $quoted = array_map(static fn (mixed $value): string => "'".$value."'", array_values($parameters));
                $expected = count($quoted) > 1
                    ? implode(", ", array_slice($quoted, 0, -1))." or ".end($quoted)
                    : implode("", $quoted);

                return [
                    "type" => "literal_error",
                    "msg" => "Input should be {$expected}",
                    "ctx" => [ "expected" => $expected, ],
                ];
            }
        }

        if ($rule === "" || $rule === "email") {
            foreach ($attributes as $attribute) {
                if (! self::isDefaultMessage($message, "validation.email", [ "attribute" => $attribute, ])) {
                    continue;
                }

                $reason = self::emailReason(is_scalar($input) ? (string) $input : "");

                return [
                    "type" => "value_error",
                    "msg" => "value is not a valid email address: {$reason}",
                    "ctx" => [ "reason" => $reason, ],
                ];
            }
        }

        return null;
    }

    /**
     * @param array<int, mixed> $rules
     * @return array{type: string, msg: string, ctx: array<string, mixed>}|null
     */
    protected static function enumConstraint(array $rules): ?array
    {
        foreach ($rules as $rule) {
            if (! $rule instanceof Enum) {
                continue;
            }

            $type = (fn (): string => $this->type)->call($rule);
            $quoted = array_map(static fn (\BackedEnum $case): string => "'".$case->value."'", $type::cases());
            $expected = count($quoted) > 1
                ? implode(", ", array_slice($quoted, 0, -1))." or ".end($quoted)
                : implode("", $quoted);

            return [
                "type" => "enum",
                "msg" => "Input should be {$expected}",
                "ctx" => [ "expected" => $expected, ],
            ];
        }

        return null;
    }

    /**
     * @param string $message
     * @param string $key
     * @param array<int, string> $attributes
     * @param string $parameter
     * @return int|null
     */
    protected static function matchingLength(string $message, string $key, array $attributes, string $parameter): ?int
    {
        for ($length = 1; $length <= 255; $length++) {
            foreach ($attributes as $attribute) {
                if (self::isDefaultMessage($message, $key, [
                    "attribute" => $attribute,
                    $parameter => $length,
                ])) {
                    return $length;
                }
            }
        }

        return null;
    }

    /**
     * @param string $message
     * @param string $key
     * @param array<string, mixed> $replace
     * @return bool
     */
    protected static function isDefaultMessage(string $message, string $key, array $replace): bool
    {
        foreach ([ "en", "id", "ms", ] as $locale) {
            if ($message === trans($key, $replace, $locale)) {
                return true;
            }
        }

        return false;
    }

    /**
     * FastAPI reports a password mismatch on the whole body.
     *
     * @param string $message
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    protected static function mismatchDetail(string $message, array $payload): array
    {
        return [
            "type" => "value_error",
            "loc" => [ "body", ],
            "msg" => $message,
            "input" => (object) $payload,
            "ctx" => [ "error" => $message, ],
        ];
    }

    /**
     * @param string $message
     * @return bool
     */
    protected static function isPasswordMismatch(string $message): bool
    {
        foreach ([ "en", "id", "ms", ] as $locale) {
            if ($message === trans("user.auth.password_mismatch", [], $locale)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $value
     * @return string
     */
    protected static function emailReason(string $value): string
    {
        $at = strpos($value, "@");

        if ($at === false) {
            return "An email address must have an @-sign.";
        }

        $local = substr($value, 0, $at);
        $domain = substr($value, $at + 1);

        if ($local === "") {
            return "There must be something before the @-sign.";
        }

        if ($domain === "") {
            return "There must be something after the @-sign.";
        }

        if (str_contains($domain, "@")) {
            return "The part after the @-sign contains invalid characters: '@'.";
        }

        if (str_ends_with($value, ".")) {
            return "An email address cannot end with a period.";
        }

        if (str_starts_with($value, ".")) {
            return "An email address cannot start with a period.";
        }

        if (str_ends_with($local, ".")) {
            return "An email address cannot have a period immediately before the @-sign.";
        }

        if (str_starts_with($domain, ".")) {
            return "An email address cannot have a period immediately after the @-sign.";
        }

        if (str_contains($value, "..")) {
            return "An email address cannot have two periods in a row.";
        }

        if (! str_contains($domain, ".")) {
            return "The part after the @-sign is not valid. It should have a period.";
        }

        return "The email address is not valid.";
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
     * @param mixed $input
     * @return mixed
     */
    protected static function resolveInput(mixed $input): mixed
    {
        return $input instanceof UploadedFile ? $input->getClientOriginalName() : $input;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $field
     * @return array<int, string|int>
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
            return array_merge([ "body", ], array_map(
                static fn (string $segment): string|int => ctype_digit($segment) ? (int) $segment : $segment,
                explode(".", $field),
            ));
        }

        return [ "body", $field, ];
    }
}
