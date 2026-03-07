<?php

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasScopedValidationRules;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains, HasScopedValidationRules;

    /**
     * @param mixed $value
     * @return string|null
     */
    public function getPrimaryColorAttribute($value): ?string
    {
        $keyVisual = $this->attributes['key_visual'] ?? null;
        if (is_array($keyVisual) && array_key_exists('primary_color', $keyVisual)) {
            return $keyVisual['primary_color'] !== null && $keyVisual['primary_color'] !== ''
                ? (string) $keyVisual['primary_color']
                : null;
        }

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function getSecondaryColorAttribute($value): ?string
    {
        $keyVisual = $this->attributes['key_visual'] ?? null;
        if (is_array($keyVisual) && array_key_exists('secondary_color', $keyVisual)) {
            return $keyVisual['secondary_color'] !== null && $keyVisual['secondary_color'] !== ''
                ? (string) $keyVisual['secondary_color']
                : null;
        }

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    public function getTertiaryColorAttribute($value): ?string
    {
        $keyVisual = $this->attributes['key_visual'] ?? null;
        if (is_array($keyVisual) && array_key_exists('tertiary_color', $keyVisual)) {
            return $keyVisual['tertiary_color'] !== null && $keyVisual['tertiary_color'] !== ''
                ? (string) $keyVisual['tertiary_color']
                : null;
        }

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setPrimaryColorAttribute($value): void
    {
        $kv = $this->attributes['key_visual'] ?? [];
        if (! is_array($kv)) {
            $kv = [];
        }
        $kv['primary_color'] = $value !== null && $value !== '' ? $value : null;
        $this->attributes['key_visual'] = $kv;
        unset($this->attributes['primary_color']);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setSecondaryColorAttribute($value): void
    {
        $kv = $this->attributes['key_visual'] ?? [];
        if (! is_array($kv)) {
            $kv = [];
        }
        $kv['secondary_color'] = $value !== null && $value !== '' ? $value : null;
        $this->attributes['key_visual'] = $kv;
        unset($this->attributes['secondary_color']);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setTertiaryColorAttribute($value): void
    {
        $kv = $this->attributes['key_visual'] ?? [];
        if (! is_array($kv)) {
            $kv = [];
        }
        $kv['tertiary_color'] = $value !== null && $value !== '' ? $value : null;
        $this->attributes['key_visual'] = $kv;
        unset($this->attributes['tertiary_color']);
    }
}
