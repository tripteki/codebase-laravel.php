<?php

namespace Src\V0\Auth\Http\Controllers\API\Auth;

use Src\V0\Auth\Http\Requests\API\Auth\LoginValidation;
use Src\V0\Auth\Http\Requests\API\Auth\LogoutValidation;
use App\Http\Controllers\Controller as BaseController;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedController extends BaseController
{
    /**
     * Give with token.
     *
     * @param mixed $token
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function withToken($token)
    {
        return iresponse(
        [
            "token" => $token,
            "type" => "bearer",
            "expires" => Auth::guard("api")->factory()->getTTL() * 60,

        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Auth"},
     *      summary="Sign in",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      description="User's Email."
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="string",
     *                      description="User's Password."
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
     * Handle an incoming auth.
     *
     * @param \App\Http\Requests\API\Auth\LoginValidation $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(LoginValidation $request): Response|JsonResponse|RedirectResponse
    {
        $data = $request->authenticate();

        if ($request->wantsJson()) {

            return $this->withToken($data);

        } else {

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        }
    }

    /**
     * @OA\Post(
     *      path="/auth/logout",
     *      tags={"Auth"},
     *      summary="Sign out",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * Destroy an authed session.
     *
     * @param \App\Http\Requests\API\Auth\LogoutValidation $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(LogoutValidation $request): Response|JsonResponse|RedirectResponse
    {
        Auth::guard("api")->logout(true);

        if ($request->wantsJson()) {

            return iresponse([], Response::HTTP_OK);

        } else {

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect("/");
        }
    }

    /**
     * @OA\Put(
     *      path="/auth/refresh",
     *      tags={"Auth"},
     *      summary="Refresh token",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=201,
     *          description="Created."
     *      ),
     *      @OA\Response(
     *          response=304,
     *          description="Not Modified."
     *      )
     * )
     *
     * Handle refresh token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request): Response|JsonResponse
    {
        if ($request->wantsJson()) {

            return $this->withToken(Auth::guard("api")->refresh());
        }

        return iresponse([], Response::HTTP_NOT_MODIFIED);
    }

    /**
     * @OA\Get(
     *      path="/auth/me",
     *      tags={"Auth"},
     *      summary="Me",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * Current authed user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function me(Request $request): Response|JsonResponse
    {
        return iresponse(Auth::user(), Response::HTTP_OK);
    }
}
