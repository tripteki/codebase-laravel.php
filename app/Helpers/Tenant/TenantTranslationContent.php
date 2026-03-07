<?php

namespace App\Helpers\Tenant;

use App\Models\ContentTrans;
use BackedEnum;
use InvalidArgumentException;
use Stancl\Tenancy\Database\TenantScope;

class TenantTranslationContent
{
    /**
     * @param string $tenantId
     * @param string $group
     * @param array $keys
     * @return void
     */
    public static function ensureKeysForTenant(string $tenantId, string $group, array $keys): void
    {
        if (! config("tenancy.is_tenancy")) {
            return;
        }

        $tenantId = trim($tenantId);
        $group = trim($group);
        if ($tenantId === "" || $group === "") {
            return;
        }

        $keys = array_values(array_unique(array_filter(array_map("trim", $keys))));
        if ($keys === []) {
            return;
        }

        $contentTransQuery = ContentTrans::withoutGlobalScope(TenantScope::class);

        foreach ($keys as $key) {
            $contentTransQuery->firstOrCreate(
                [
                    "tenant_id" => $tenantId,
                    "group" => $group,
                    "key" => $key,
                ],
                [
                    "value" => null,
                ],
            );
        }
    }

    /**
     * @param string $tenantId
     * @param string $enumClass
     * @return void
     */
    public static function ensureForBackedEnum(string $tenantId, string $enumClass): void
    {
        if (! is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidArgumentException("Enum class must be a backed enum: " . $enumClass);
        }

        if (! is_callable([$enumClass, "contentGroup"])) {
            throw new InvalidArgumentException("Enum must define public static function contentGroup(): " . $enumClass);
        }

        $group = trim((string) $enumClass::contentGroup());
        if ($group === "") {
            throw new InvalidArgumentException("Enum contentGroup() must return non-empty string: " . $enumClass);
        }

        $keys = array_map(static fn (BackedEnum $case) => (string) $case->value, $enumClass::cases());

        self::ensureKeysForTenant($tenantId, $group, $keys);
    }

    /**
     * @param string $tenantId
     * @param array $enumClasses
     * @return void
     */
    public static function ensureForBackedEnums(string $tenantId, array $enumClasses): void
    {
        foreach ($enumClasses as $enumClass) {
            self::ensureForBackedEnum($tenantId, $enumClass);
        }
    }
}
