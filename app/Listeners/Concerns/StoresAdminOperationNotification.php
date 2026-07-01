<?php

namespace App\Listeners\Concerns;

use App\Models\User;
use Illuminate\Support\Str;

trait StoresAdminOperationNotification
{
    /**
     * @param string $userId
     * @param string $type
     * @param array<string, mixed> $data
     * @return void
     */
    protected function storeAdminOperationNotification(string $userId, string $type, array $data): void
    {
        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $user->notifications()->create([
            "id" => Str::uuid()->toString(),
            "type" => $type,
            "data" => $data,
        ]);
    }
}
