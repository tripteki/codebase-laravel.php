<?php

namespace Modules\Auth\App\Dtos;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class AuthLoginDto extends Data
{
    /**
     * @param string|null $identifierKey
     * @param string|null $identifierValue
     * @param string|null $identifier
     * @param string|null $password
     * @param bool|null $remember
     * @return void
     */
    public function __construct(
        public ?string $identifierKey = null,
        public ?string $identifierValue = null,
        public ?string $identifier = null,
        public ?string $password = null,
        public ?bool $remember = null,
    ) {}

    /**
     * @return bool
     */
    public function rememberMe(): bool
    {
        return filter_var($this->remember, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return string
     */
    public function field(): string
    {
        if ($this->identifierKey !== null && in_array($this->identifierKey, ["email", "name"], true)) {
            return $this->identifierKey;
        }

        $value = $this->credentialValue();

        return filter_var($value, FILTER_VALIDATE_EMAIL) ? "email" : "name";
    }

    /**
     * @return string
     */
    public function credentialValue(): string
    {
        if ($this->identifierValue !== null && $this->identifierValue !== "") {
            return $this->identifierValue;
        }

        return (string) $this->identifier;
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            "identifierKey" => [ "nullable", "string", "in:email,name", ],
            "identifierValue" => [ "required_without:identifier", "nullable", "string", ],
            "identifier" => [ "required_without:identifierValue", "nullable", "string", ],
            "password" => [ "required", "string", "min:8", ],
            "remember" => [ "nullable", "boolean", ],
        ];
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
