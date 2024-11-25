<?php

namespace Src\V1\Api\Auth\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Support\Validation\ValidationContext;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AuthLoginDto extends Data
{
    #[Computed]
    /**
     * @var string
     */
    public string $field;

    /**
     * @param string|null $identifier
     * @param string|null $password
     *
     * @param string|null $expires
     * @param string|null $type
     * @param string|null $token
     * @return void
     */
    public function __construct(
        public ?string $identifier,
        public ?string $password,

        public ?string $expires,
        public ?string $type,
        public ?string $token
    ) {
        $this->field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? "email" : "name";
    }

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        return [

            "identifier" => [

                "required",
                "string",
            ],

            "password" => [

                "required",
                "string",
            ],
        ];
    }
}
