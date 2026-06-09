<?php

namespace Modules\Auth\App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class PasswordResetTokenHelper
{
    /**
     * @param string $email
     * @param string $token
     * @return void
     */
    public static function upsertSigned(string $email, string $token): void
    {
        DB::table("password_reset_tokens")->updateOrInsert(
            self::lookupKey($email),
            [
                "token" => $token,
                "created_at" => now(),
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return string
     */
    public static function storeBrokerToken(User $user): string
    {
        $plainToken = Str::random(64);

        self::upsertBroker($user->email, Hash::make($plainToken));

        return $plainToken;
    }

    /**
     * @param string $email
     * @param string $hashedToken
     * @return void
     */
    public static function upsertBroker(string $email, string $hashedToken): void
    {
        DB::table("password_reset_tokens")->updateOrInsert(
            self::lookupKey($email),
            [
                "token" => $hashedToken,
                "created_at" => now(),
            ],
        );
    }

    /**
     * @param string $email
     * @param string $token
     * @return object|null
     */
    public static function findSigned(string $email, string $token): ?object
    {
        return DB::table("password_reset_tokens")
            ->where(self::lookupKey($email))
            ->where("token", $token)
            ->first();
    }

    /**
     * @param string $email
     * @param string $plainToken
     * @return bool
     */
    public static function verifyBrokerToken(string $email, string $plainToken): bool
    {
        $record = DB::table("password_reset_tokens")
            ->where(self::lookupKey($email))
            ->first();

        if ($record === null || ! isset($record->created_at)) {
            return false;
        }

        $expire = (int) config("auth.passwords.users_eloquent.expire", 60);

        if (Carbon::parse($record->created_at)->addMinutes($expire)->isPast()) {
            return false;
        }

        return Hash::check($plainToken, (string) $record->token);
    }

    /**
     * @param string $email
     * @return void
     */
    public static function delete(string $email): void
    {
        DB::table("password_reset_tokens")
            ->where(self::lookupKey($email))
            ->delete();
    }

    /**
     * @param string $email
     * @return array<string, string>
     */
    private static function lookupKey(string $email): array
    {
        return [
            "email" => $email,
        ];
    }
}
