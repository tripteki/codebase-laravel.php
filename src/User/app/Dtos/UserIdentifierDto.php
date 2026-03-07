<?php

namespace Modules\User\App\Dtos;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Exists;

class UserIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
    #[Exists(User::class)]
    /**
     * @var string|null
     */
    public ?string $id;

    /**
     * @return bool
     */
    public static function authorize(): bool
    {
        return true;
    }
}
