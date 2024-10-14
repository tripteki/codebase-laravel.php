<?php

namespace Src\V0\Auth\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewPasswordController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/auth/reset-password",
     *      tags={"Auth"},
     *      summary="Reset credential",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="token",
     *                      type="string",
     *                      description="Reset's Token."
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="User's Email."
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="User's Password."
     *                  ),
     *                  @OA\Property(
     *                      property="password_confirmation",
     *                      type="string",
     *                      description="User's Password Confirmation."
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * Handle an incoming new password.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response|JsonResponse|RedirectResponse
    {
        $request->validate([
            "token" => [ "required", ],
            "email" => [ "required", "email", ],
            "password" => [ "required", "confirmed", Rules\Password::defaults(), ],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function ($user) use ($request) {
                $user->forceFill([
                    "password" => Hash::make($request->password),
                    "remember_token" => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson()) {

            if ($status != Password::PASSWORD_RESET) {
                throw ValidationException::withMessages([
                    "email" => [__($status)],
                ]);
            }

            return iresponse([ "status" => __($status), ], Response::HTTP_CREATED);

        } else {

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            return $status == Password::PASSWORD_RESET
                    ? redirect()->route("login")->with("status", __($status))
                    : back()->withInput($request->only("email"))
                            ->withErrors(["email" => __($status)]);
        }
    }
}
