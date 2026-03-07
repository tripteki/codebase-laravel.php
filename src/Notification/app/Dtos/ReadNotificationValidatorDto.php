<?php

namespace Modules\Notification\App\Dtos;

use Illuminate\Notifications\DatabaseNotification;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\FromRouteParameter;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class ReadNotificationValidatorDto extends Data
{
    #[FromRouteParameter("id")]
    #[Uuid(), Exists(
        table: DatabaseNotification::class,
        deletedAtColumn: "read_at",
        withoutTrashed: false,
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
