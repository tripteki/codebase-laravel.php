<?php

namespace App\Helpers;

class CopyrightHelper
{
    /**
     * @param string|null $displayName
     * @return string
     */
    public static function footer(?string $displayName = null): string
    {
        $displayName = trim((string) ($displayName ?? AppNameHelper::format()));

        if ($displayName === "") {
            $displayName = AppNameHelper::format();
        }

        return "© ".date("Y")." ".$displayName;
    }
}
