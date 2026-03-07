<?php

namespace Modules\Notification\App\Dtos;

use Modules\Notification\App\Models\Notification;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Attributes\Validation\Exists;

class UnreadNotificationValidatorDto extends Data
{
    #[FromRouteParameter("id")]
    #[Uuid(), Exists(
        table: Notification::class,
        deletedAtColumn: "read_at",
        withoutTrashed: true,
    )]
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
