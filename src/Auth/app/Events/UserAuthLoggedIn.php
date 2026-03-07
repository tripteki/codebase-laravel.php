<?php

namespace Modules\Auth\App\Events;

use Modules\User\App\Dtos\UserTransformerDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAuthLoggedIn
{
    use Dispatchable, SerializesModels;

    /**
     * @param \Modules\User\App\Dtos\UserTransformerDto $user
     * @return void
     */
    public function __construct(
        public UserTransformerDto $user,
    ) {
    }
}
