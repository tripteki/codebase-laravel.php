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
     * @param string|null $tenantId
     * @return string|null
     */
    public static function normalizeTenantId(?string $tenantId): ?string
    {
        if (! is_string($tenantId) || trim($tenantId) === "") {
            return null;
        }

        return trim($tenantId);
    }

    /**
     * @param string $email
     * @param string $token
     * @param string|null $tenantId
     * @return void
     */
    public static function upsertSigned(string $email, string $token, ?string $tenantId = null): void
    {
        DB::table("password_reset_tokens")->updateOrInsert(
            self::lookupKey($email, $tenantId),
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

        self::upsertBroker(
            $user->email,
            Hash::make($plainToken),
            $user->tenant_id !== null ? (string) $user->tenant_id : null,
        );

        return $plainToken;
    }

    /**
     * @param string $email
     * @param string $hashedToken
     * @param string|null $tenantId
     * @return void
     */
    public static function upsertBroker(string $email, string $hashedToken, ?string $tenantId = null): void
    {
        DB::table("password_reset_tokens")->updateOrInsert(
            self::lookupKey($email, $tenantId),
            [
                "token" => $hashedToken,
                "created_at" => now(),
            ],
        );
    }

    /**
     * @param string $email
     * @param string $token
     * @param string|null $tenantId
     * @return object|null
     */
    public static function findSigned(string $email, string $token, ?string $tenantId = null): ?object
    {
        return DB::table("password_reset_tokens")
            ->where(self::lookupKey($email, $tenantId))
            ->where("token", $token)
            ->first();
    }

    /**
     * @param string $email
     * @param string $plainToken
     * @param string|null $tenantId
     * @return bool
     */
    public static function verifyBrokerToken(string $email, string $plainToken, ?string $tenantId = null): bool
    {
        $record = DB::table("password_reset_tokens")
            ->where(self::lookupKey($email, $tenantId))
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
     * @param string|null $tenantId
     * @return void
     */
    public static function delete(string $email, ?string $tenantId = null): void
    {
        DB::table("password_reset_tokens")
            ->where(self::lookupKey($email, $tenantId))
            ->delete();
    }

    /**
     * @param string $email
     * @param string|null $tenantId
     * @return array<string, string|null>
     */
    private static function lookupKey(string $email, ?string $tenantId): array
    {
        return [
            "email" => $email,
            "tenant_id" => self::normalizeTenantId($tenantId) ?? "",
        ];
    }
}
