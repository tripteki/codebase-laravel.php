<?php

namespace Modules\Notification\App\Dtos;

use Spatie\LaravelData\Data;

class NotificationUnreadTransformerDto extends Data
{
    /**
     * @param int $unread
     * @return void
     */
    public function __construct(
        public int $unread,
    ) {
    }
}
