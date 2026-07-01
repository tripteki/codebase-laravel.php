<?php

namespace Modules\Notification\App\Dtos;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\FromRouteParameter;

class UnreadNotificationValidatorDto extends Data
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
