<?php

/**
 * @param string $path
 * @param array $query
 * @return string
 */
if (! function_exists("frontend_url")) {
    function frontend_url(string $path = "", array $query = []): string
    {
        $base = rtrim((string) (config("app.frontend_url") ?: config("app.url")), "/");
        $path = ltrim($path, "/");
        $url = $path !== "" ? $base."/".$path : $base;

        if ($query !== []) {
            $url .= "?".http_build_query($query);
        }

        return $url;
    }
}

/**
 * @param string $url
 * @return string
 */
if (! function_exists("signed_url")) {
    function signed_url(string $url): string
    {
        $signature = hash_hmac("sha256", $url, (string) config("app.key"));

        return $url.(str_contains($url, "?") ? "&" : "?")."signed=".urlencode($signature);
    }
}

/**
 * @param string $url
 * @param string|null $signed
 * @return bool
 */
if (! function_exists("verify_signed_url")) {
    function verify_signed_url(string $url, ?string $signed): bool
    {
        if (! is_string($signed) || $signed === "") {
            return false;
        }

        $expected = hash_hmac("sha256", $url, (string) config("app.key"));

        return hash_equals($expected, $signed);
    }
}

/**
 * @param string $email
 * @return string
 */
if (! function_exists("auth_email_path_segment")) {
    function auth_email_path_segment(string $email): string
    {
        return rawurlencode($email);
    }
}

/**
 * @param string $email
 * @return string
 */
if (! function_exists("auth_verify_email_path")) {
    function auth_verify_email_path(string $email): string
    {
        return "auth/verify-email/".auth_email_path_segment($email);
    }
}

/**
 * @param string $email
 * @return string
 */
if (! function_exists("auth_reset_password_path")) {
    function auth_reset_password_path(string $email): string
    {
        return "auth/reset-password/".auth_email_path_segment($email);
    }
}

/**
 * @param string $path
 * @return string
 */
if (! function_exists("signed_frontend_url")) {
    function signed_frontend_url(string $path): string
    {
        return signed_url(frontend_url($path));
    }
}

/**
 * @param string $path
 * @param string|null $signed
 * @return bool
 */
if (! function_exists("verify_auth_signed_url")) {
    function verify_auth_signed_url(string $path, ?string $signed): bool
    {
        return verify_signed_url(frontend_url($path), $signed);
    }
}

/**
 * @param \Illuminate\Http\Request $request
 * @return string
 */
if (! function_exists("signed_request_frontend_url")) {
    function signed_request_frontend_url(\Illuminate\Http\Request $request): string
    {
        $path = "/".$request->path();
        $path = preg_replace("#^/api/v1#", "", $path);
        $path = preg_replace("#^/api#", "", $path);
        $path = preg_replace("#^/v1#", "", $path);

        return frontend_url(ltrim($path, "/"));
    }
}
