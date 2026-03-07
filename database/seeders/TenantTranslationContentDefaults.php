<?php

namespace Database\Seeders;

use Database\Seeders\Stage\Enum\TenantAuth;

class TenantTranslationContentDefaults
{
    /**
     * @return array<int, class-string<\BackedEnum>>
     */
    public static function backedEnums(): array
    {
        if (! config("tenancy.is_tenancy")) {
            return [];
        }

        return [
            TenantAuth::class,
        ];
    }
}
