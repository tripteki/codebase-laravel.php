<?php

namespace Modules\Notification\App\Dtos;

use Spatie\LaravelData\Data;

class NotificationCountTransformerDto extends Data
{
    /**
     * @param int $count
     * @return void
     */
    public function __construct(
        public int $count,
    ) {
    }
}
