<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('v1.users.{userId}', function (User $user, int|string $userId) {

    return true;
});
