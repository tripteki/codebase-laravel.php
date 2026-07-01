<?php

namespace Modules\Log\App\Dtos;

use Modules\Log\App\Models\Activity;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class ActivityIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
    #[Exists(Activity::class)]
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
