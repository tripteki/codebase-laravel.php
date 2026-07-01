<?php

namespace Modules\Log\App\Dtos;

use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Data;

class ActivityIdentifierDto extends Data
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
