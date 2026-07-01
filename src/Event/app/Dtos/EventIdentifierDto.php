<?php

namespace Modules\Event\App\Dtos;

use Modules\Event\App\Models\Event;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class EventIdentifierDto extends Data
{
    #[FromRouteParameter("id")]
    #[Exists(Event::class, "id"), ]
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
