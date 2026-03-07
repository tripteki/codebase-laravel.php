<?php

use App\Enum\Event\AddOnEnum;
use App\Models\ContentTrans;
use App\Models\Tenant;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @return bool
 */
if (! function_exists("has_tenant")) {

    function has_tenant(): bool
    {
        return config("tenancy.is_tenancy") && function_exists("tenancy") && tenancy()->initialized;
    }
}

/**
 * @return bool
 */
if (! function_exists("hasTenant")) {

    function hasTenant(): bool
    {
        return has_tenant();
    }
}

/**
 * @return bool
 */
if (! function_exists("is_central")) {

    function is_central(): bool
    {
        return ! hasTenant();
    }
}

/**
 * @return bool
 */
if (! function_exists("isCentral")) {

    function isCentral(): bool
    {
        return is_central();
    }
}

/**
 * @return bool
 */
if (! function_exists("is_tenant")) {

    function is_tenant(): bool
    {
        return hasTenant();
    }
}

/**
 * @return bool
 */
if (! function_exists("isTenant")) {

    function isTenant(): bool
    {
        return is_tenant();
    }
}



/**
 * Current request-response authority.
 *
 * @param \Illuminate\Http\Request|null $request
 * @return string
 */
if (! function_exists("tenant_public_authority")) {

    function tenant_public_authority(?Request $request = null): string
    {
        $request ??= request();
        $host = $request->getHttpHost();
        if ($host !== "") {
            return $host;
        }

        $parsed = parse_url((string) config("app.url"));
        $host = $parsed["host"];
        $port = $parsed["port"] ?? null;
        if ($port !== null && ! in_array((int) $port, [80, 443], true)) {
            return $host . ":" . $port;
        }

        return $host;
    }
}

/**
 * Label for path tenancy.
 *
 * @param \Illuminate\Http\Request|null $request
 * @return string
 */
if (! function_exists("tenant_public_path_label")) {

    function tenant_public_path_label(string $slug, ?Request $request = null): string
    {
        return tenant_public_authority($request) . "/" . ltrim($slug, "/");
    }
}

/**
 * Full URL for path-based tenancy.
 *
 * @param \Illuminate\Http\Request|null $request
 * @return string
 */
if (! function_exists("tenant_public_path_url")) {

    function tenant_public_path_url(string $slug, ?Request $request = null): string
    {
        $request ??= request();
        $slug = ltrim($slug, "/");
        $scheme = $request->getHttpHost() !== ""
            ? $request->getScheme()
            : ((string) parse_url((string) config("app.url"), PHP_URL_SCHEME) ?: "http");

        return $scheme . "://" . tenant_public_authority($request) . "/" . $slug;
    }
}

/**
 * Full URL for subdomain or custom domain (non-path tenancy).
 *
 * @param \Illuminate\Http\Request|null $request
 * @return string
 */
if (! function_exists("tenant_public_domain_url")) {

    function tenant_public_domain_url(string $domainPart, string $centralHostFromConfig, ?Request $request = null): string
    {
        $request ??= request();
        $domainPart = trim($domainPart);
        if ($domainPart === "") {
            return "";
        }

        $displayHost = str_contains($domainPart, ".")
            ? $domainPart
            : $domainPart . "." . $centralHostFromConfig;

        $scheme = $request->getHttpHost() !== ""
            ? $request->getScheme()
            : ((string) parse_url((string) config("app.url"), PHP_URL_SCHEME) ?: "http");

        $defaultPort = $scheme === "https" ? 443 : 80;
        $port = $request->getHttpHost() !== ""
            ? (int) $request->getPort()
            : (int) (parse_url((string) config("app.url"), PHP_URL_PORT) ?: $defaultPort);

        $portSuffix = in_array($port, [80, 443], true) ? "" : ":" . $port;

        return $scheme . "://" . $displayHost . $portSuffix;
    }
}

/**
 * @param string|array $domainOrRoute
 * @param string|array|null $route
 * @param array $parameters
 * @param bool $absolute
 * @return string
 */
if (! function_exists("tenant_routes")) {

    function tenant_routes($domainOrRoute, $route = null, $parameters = [], $absolute = true)
    {
        static $routeNameLookupsRefreshed = false;

        $resolveRouteInput = static function ($domainOrRoute, $route, $parameters): array {
            if ($route === null) {
                return [$domainOrRoute, $parameters, null, null];
            }

            if (is_string($route)) {
                return [$route, $parameters, $domainOrRoute, null];
            }

            return [$domainOrRoute, $route, null, null];
        };

        [$routeName, $routeParameters, $domain, $tenantId] = $resolveRouteInput($domainOrRoute, $route, $parameters);

        if (! config("tenancy.is_tenancy")) {

            try {
                return route($routeName, $routeParameters, $absolute);
            } catch (\Throwable $e) {
                return "#";
            }
        }

        if (! $routeNameLookupsRefreshed) {

            Route::getRoutes()->refreshNameLookups();

            $routeNameLookupsRefreshed = true;
        }

        $middleware = config("tenancy.tenant_identification_middleware");
        $isPathBased = $middleware && str_contains((string) $middleware, "InitializeTenancyByPath");

        $currentTenant = config("tenancy.is_tenancy") ? tenant() : null;
        $isCentral = function_exists("hasTenant") ? ! hasTenant() : $currentTenant === null;

        if ($domain === null && $tenantId === null && $currentTenant) {
            if ($isPathBased) {
                $tenantId = $currentTenant->id;
            } else {
                $tenantDomain = $currentTenant->domains()->first();
                $domain = $tenantDomain ? $tenantDomain->domain : request()->getHost();
            }
        }

        if ($domain === null && $tenantId === null) {
            $domain = request()->getHost();
        }

        $pathPrefix = null;
        if ($isPathBased) {
            $resolveTenantPathPrefix = static function ($tenantId): ?string {
                if ($tenantId) {
                    return (string) $tenantId;
                }

                $reserved = ["admin", "livewire", "api", "sanctum", "broadcasting", "_ignition"];
                $firstRequestSegment = request()->segments()[0] ?? null;
                if ($firstRequestSegment && ! in_array($firstRequestSegment, $reserved, true)) {
                    return $firstRequestSegment;
                }

                $referer = request()->header("Referer");
                if (! $referer) {
                    return null;
                }

                $refererPath = parse_url($referer, PHP_URL_PATH);
                $refererPath = $refererPath !== null ? trim($refererPath, "/") : "";
                $firstRefererSegment = $refererPath !== "" ? explode("/", $refererPath)[0] ?? null : null;
                if ($firstRefererSegment && ! in_array($firstRefererSegment, $reserved, true)) {
                    return $firstRefererSegment;
                }

                return null;
            };

            $pathPrefix = $resolveTenantPathPrefix($tenantId);
        }

        $routeCollection = Route::getRoutes();
        $routeCollection->refreshNameLookups();

        $candidates = [];
        foreach ($routeCollection as $r) {
            if ($r->getName() === $routeName) {
                $candidates[] = $r;
            }
        }

        if ($candidates === []) {
            return "#";
        }

        $wantsTenantRoute = $isPathBased && (! $isCentral || $pathPrefix !== null);
        $selected = null;

        $isTenantRouteCandidate = static function ($route): bool {
            $uri = (string) $route->uri();

            return str_contains($uri, "{tenant}") || in_array("tenant", $route->parameterNames(), true);
        };

        foreach ($candidates as $candidate) {
            if ($isTenantRouteCandidate($candidate) === $wantsTenantRoute) {
                $selected = $candidate;
                break;
            }
        }

        if (! $selected) {
            $selected = $candidates[count($candidates) - 1];
        }

        try {
            $url = app("url")->toRoute($selected, $routeParameters, $absolute);
        } catch (UrlGenerationException $e) {
            if (! $wantsTenantRoute || $pathPrefix === null) {
                return "#";
            }

            $hasTenantParam = is_array($routeParameters) && array_key_exists("tenant", $routeParameters);
            if ($hasTenantParam) {
                return "#";
            }

            try {
                $routeParamsWithTenant = is_array($routeParameters)
                    ? array_merge(["tenant" => $pathPrefix], $routeParameters)
                    : ["tenant" => $pathPrefix, $routeParameters];

                $url = app("url")->toRoute($selected, $routeParamsWithTenant, $absolute);
            } catch (\Throwable $e2) {
                return "#";
            }
        } catch (\InvalidArgumentException $e) {
            return "#";
        }

        if ($isPathBased && $wantsTenantRoute && $pathPrefix !== null) {
            $parsed = parse_url($url);
            $normalizedPath = ltrim($parsed["path"] ?? "/", "/");
            if (! str_starts_with($normalizedPath, $pathPrefix . "/") && $normalizedPath !== $pathPrefix) {
                $normalizedPath = $pathPrefix . "/" . $normalizedPath;
            }
            $path = "/" . ltrim($normalizedPath, "/");

            $url = ($parsed["scheme"] ?? "") . "://" .
                   ($parsed["host"] ?? "") .
                   (isset($parsed["port"]) ? ":" . $parsed["port"] : "") .
                   $path .
                   (isset($parsed["query"]) ? "?" . $parsed["query"] : "") .
                   (isset($parsed["fragment"]) ? "#" . $parsed["fragment"] : "");

        } elseif ($domain) {
            $hostname = parse_url($url, PHP_URL_HOST);
            if ($hostname) {
                $url = str_replace($hostname, $domain, $url);
            }
        }

        return $url;
    }
}

/**
 * @param string $key
 * @param array $replace
 * @param string|null $locale
 * @return string
 */
if (! function_exists("tenant_trans")) {

    function tenant_trans(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale !== null && $locale !== "" ? $locale : app()->getLocale();
        $fallbackTranslate = static fn (): string => __($key, $replace, $locale);

        if (! str_contains($key, ".")) {
            return $fallbackTranslate();
        }

        [$group, $itemKey] = explode(".", $key, 2);

        if ($group === "" || $itemKey === "") {
            return $fallbackTranslate();
        }

        if (! config("tenancy.is_tenancy") || ! hasTenant()) {
            return $fallbackTranslate();
        }

        $tenantKey = (string) tenant()->getTenantKey();

        static $resolved = [];

        $memoKey = $tenantKey . "\0" . $locale . "\0" . $group . "\0" . $itemKey;

        if (! array_key_exists($memoKey, $resolved)) {
            $row = ContentTrans::query()
                ->where("tenant_id", $tenantKey)
                ->where("group", $group)
                ->where("key", $itemKey)
                ->first();

            $text = null;
            if ($row !== null && $row->hasTranslation("value", $locale)) {
                $candidate = (string) ($row->getTranslation("value", $locale, false) ?? "");
                if ($candidate !== "") {
                    $text = $candidate;
                }
            }

            $resolved[$memoKey] = $text;
        }

        $line = $resolved[$memoKey];

        if ($line === null) {
            return $fallbackTranslate();
        }

        if ($replace === []) {
            return $line;
        }

        foreach ($replace as $k => $v) {
            $k = (string) $k;
            $v = (string) $v;
            $line = str_replace(
                [
                    ":" . $k,
                    ":" . Str::upper($k),
                    ":" . Str::ucfirst($k),
                    ":" . Str::camel($k),
                ],
                [$v, $v, $v, $v],
                $line,
            );
        }

        return $line;
    }
}

/**
 * @param \App\Models\Tenant $tenant
 * @return array
 */
if (! function_exists("tenant_mailers")) {

    function tenant_mailers(Tenant $tenant): array
    {
        $defaultMailer = [Mail::mailer(), null, null];
        $features = $tenant->getAttribute("add_ons_features");
        $featuresList = is_array($features) ? $features : [];
        if (! in_array(AddOnEnum::FEATURES_MAILING->value, $featuresList, true)) {
            return $defaultMailer;
        }

        $addOnsConfig = $tenant->getAttribute("add_ons_config");
        $mailSmtp = is_array($addOnsConfig) && isset($addOnsConfig[AddOnEnum::FEATURES_MAILING->value])
            ? $addOnsConfig[AddOnEnum::FEATURES_MAILING->value]
            : $tenant->getAttribute("mail_smtp");
        if (! is_array($mailSmtp)) {
            return $defaultMailer;
        }

        $fromAddress = ! empty($mailSmtp["from_address"]) ? (string) $mailSmtp["from_address"] : null;
        $fromName = ! empty($mailSmtp["from_name"]) ? (string) $mailSmtp["from_name"] : null;

        if (empty($mailSmtp["host"] ?? null)) {
            return [Mail::mailer(), $fromAddress, $fromName];
        }

        $password = null;
        if (! empty($mailSmtp["password"])) {
            try {
                $password = Crypt::decryptString($mailSmtp["password"]);
            } catch (\Throwable) {
                $password = null;
            }
        }

        $mailerName = "tenant_smtp_" . $tenant->id;
        Config::set("mail.mailers.{$mailerName}", [
            "transport" => "smtp",
            "host" => $mailSmtp["host"] ?? config("mail.mailers.smtp.host"),
            "port" => (int) ($mailSmtp["port"] ?? 587),
            "encryption" => $mailSmtp["encryption"] ?? "tls",
            "username" => $mailSmtp["username"] ?? null,
            "password" => $password,
            "timeout" => null,
        ]);

        return [Mail::mailer($mailerName), $fromAddress, $fromName];
    }
}
