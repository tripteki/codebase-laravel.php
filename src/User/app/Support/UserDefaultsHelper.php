<?php

namespace Modules\User\App\Support;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Modules\User\Database\Seeders\CreateUserSeeder;

final class UserDefaultsHelper
{
    /**
     * @return string
     */
    public static function defaultPassword(): string
    {
        return CreateUserSeeder::DEFAULT_PASSWORD;
    }

    /**
     * @param string $email
     * @return string
     */
    public static function usernameFromEmail(string $email): string
    {
        $localPart = Str::before(trim($email), "@");

        return $localPart !== "" ? $localPart : trim($email);
    }

    /**
     * @param string $fullName
     * @return string
     */
    public static function slugifyFullNameForEmail(string $fullName): string
    {
        return Str::slug(trim($fullName), "_");
    }

    /**
     * @param string $fullName
     * @param string|null $tenantId
     * @return string|null
     */
    public static function buildSuggestedEmail(string $fullName, ?string $tenantId = null): ?string
    {
        $localPart = self::slugifyFullNameForEmail($fullName);

        if ($localPart === "") {
            return null;
        }

        $domain = trim((string) config("app.email_server"));

        if ($domain === "") {
            return null;
        }

        if (filled($tenantId)) {
            $localPart .= ".".$tenantId;
        }

        return $localPart."@".$domain;
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return string|null
     */
    public static function resolveTenantIdForValidation(?array $payload = null): ?string
    {
        if (AdminTenancySupport::payloadHasTenant($payload)) {
            return AdminTenancySupport::resolveTenantIdFromPayload($payload);
        }

        $current = current_tenant_id();

        if ($current !== null) {
            return $current;
        }

        $user = Auth::user();

        if ($user instanceof User && filled($user->tenant_id)) {
            return (string) $user->tenant_id;
        }

        return null;
    }

    /**
     * @param string|null $tenantId
     * @param mixed $ignore
     * @return Unique
     */
    public static function uniqueEmailRule(?string $tenantId, mixed $ignore = null): Unique
    {
        $rule = Rule::unique(User::class);

        if ($ignore !== null) {
            $rule->ignore($ignore);
        }

        if (filled($tenantId)) {
            return $rule->where("tenant_id", $tenantId);
        }

        return $rule->whereNull("tenant_id");
    }
}
