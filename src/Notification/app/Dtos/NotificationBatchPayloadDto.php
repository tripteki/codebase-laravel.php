<?php

namespace Modules\Notification\App\Dtos;

use Spatie\LaravelData\Data;

class NotificationBatchPayloadDto extends Data
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
