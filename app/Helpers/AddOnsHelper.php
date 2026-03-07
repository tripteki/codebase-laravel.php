<?php

namespace App\Helpers;

use App\Enum\Event\AddOnEnum;
use App\Models\Tenant;

class AddOnsHelper
{
    /**
     * @param \App\Enum\Event\AddOnEnum $addOn
     * @return bool
     */
    public static function has(AddOnEnum $addOn): bool
    {
        if (! config("tenancy.is_tenancy") || ! function_exists("tenant")) {
            return false;
        }

        $tenant = tenant();
        if (! $tenant instanceof Tenant) {
            return false;
        }

        $list = match (true) {
            $addOn->isFeature() => self::tenantFeatureValues($tenant),
            $addOn->isModule() => self::tenantModuleValues($tenant),
            default => [],
        };

        return in_array($addOn->value, $list, true);
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return array<\App\Enum\Event\AddOnEnum>
     */
    public static function enabledFeatureCases(Tenant $tenant): array
    {
        $values = self::tenantFeatureValues($tenant);
        $allowed = array_flip($values);

        return array_values(array_filter(AddOnEnum::features(), fn (AddOnEnum $c) => isset($allowed[$c->value])));
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return array<string, array<\App\Enum\Event\AddOnEnum>>
     */
    public static function enabledModuleCasesGroupedByCategory(Tenant $tenant): array
    {
        $values = self::tenantModuleValues($tenant);
        $allowed = array_flip($values);
        $allGroups = AddOnEnum::modulesGroupedByCategory();
        $result = [];
        foreach ($allGroups as $categoryKey => $modules) {
            $enabled = array_values(array_filter($modules, fn (AddOnEnum $c) => isset($allowed[$c->value])));
            if ($enabled !== []) {
                $result[$categoryKey] = $enabled;
            }
        }

        return $result;
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return array<string>
     */
    protected static function tenantFeatureValues(Tenant $tenant): array
    {
        $raw = $tenant->getAttribute("add_ons_features");
        $parsed = self::parseAddOnsToArray($raw);
        if ($parsed !== []) {
            return $parsed;
        }

        return array_map(fn (AddOnEnum $c) => $c->value, AddOnEnum::features());
    }

    /**
     * @param \App\Models\Tenant $tenant
     * @return array<string>
     */
    protected static function tenantModuleValues(Tenant $tenant): array
    {
        $raw = $tenant->getAttribute("add_ons_modules");
        $parsed = self::parseAddOnsToArray($raw);
        if ($parsed !== []) {
            return $parsed;
        }

        $modules = array_filter(AddOnEnum::cases(), fn (AddOnEnum $c) => $c->isModule());

        return array_map(fn (AddOnEnum $c) => $c->value, $modules);
    }

    /**
     * @param mixed $raw
     * @return array<string>
     */
    protected static function parseAddOnsToArray($raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map("trim", $raw)));
        }
        if (! is_string($raw) || trim($raw) === "") {
            return [];
        }

        return array_values(array_filter(array_map("trim", explode(",", $raw))));
    }



    /**
     * @return string|null
     */
    public static function copyrightSidebarCompanyName(): ?string
    {
        if (! config("tenancy.is_tenancy") || ! function_exists("tenant")) {
            return null;
        }

        $tenant = tenant();
        if (! $tenant instanceof Tenant) {
            return null;
        }

        if (! self::has(AddOnEnum::FEATURES_COPYRIGHT)) {
            return null;
        }

        $addOnsConfig = $tenant->getAttribute("add_ons_config");
        if (! is_array($addOnsConfig)) {
            return null;
        }

        $slice = $addOnsConfig[AddOnEnum::FEATURES_COPYRIGHT->value] ?? null;
        if (! is_array($slice)) {
            return null;
        }

        $name = $slice["owner"] ?? null;
        if (! is_string($name)) {
            return null;
        }
        $name = trim($name);

        return $name !== "" ? $name : null;
    }
}
