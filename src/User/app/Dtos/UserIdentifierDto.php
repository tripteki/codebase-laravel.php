<?php

namespace Modules\User\App\Dtos;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\FromRouteParameter;

class UserIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
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
