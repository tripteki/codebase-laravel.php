<?php

namespace App\Http\Requests\API\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Attempt to authenticate the incoming credentials.
     *
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): mixed
    {
        $auth = null;
        $fields = $this->only("email", "password");

        $this->ensureIsNotRateLimited();

        if ($this->wantsJson()) $auth = Auth::guard("api")->attempt($fields);
        else $auth = Auth::guard("web")->attempt($fields, $this->boolean("remember"));

        if (! $auth) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                "email" => __("auth.failed"),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $auth;
    }

    /**
     * Get the validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "email" => [ "required", "string", "email", ],
            "password" => [ "required", "string", ],
        ];
    }

    /**
     * Ensure the login is not rate limited.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            "email" => trans("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input("email"))."|".$this->ip());
    }
}
