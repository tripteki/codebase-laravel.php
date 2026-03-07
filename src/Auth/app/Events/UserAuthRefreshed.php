<?php

namespace Modules\Auth\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAuthRefreshed
{
    use Dispatchable, SerializesModels;

    /**
     * @param string $userId
     * @return void
     */
    public function __construct(
        public string $userId,
    ) {
    }
}
