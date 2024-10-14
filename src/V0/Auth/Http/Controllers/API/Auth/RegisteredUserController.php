<?php

namespace Src\V0\Auth\Http\Controllers\API\Auth;

use Error;
use Exception;
use App\Models\User;
use Src\V0\Auth\Http\Requests\API\Auth\RegistrationStoreValidation;
use App\Http\Controllers\Controller as BaseController;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/auth/register",
     *      tags={"Auth"},
     *      summary="Registration",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      description="User's Name."
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
     *          response=202,
     *          description="Accepted."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * Handle an incoming registration.
     *
     * @param \App\Http\Requests\API\Auth\RegistrationStoreValidation $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(RegistrationStoreValidation $request): Response|JsonResponse|RedirectResponse
    {
        $form = $request->validated();
        $data = [];
        $statecode = Response::HTTP_ACCEPTED;

        DB::beginTransaction();

        try {

            $data = User::create(
            [
                "name" => $form["name"],
                "email" => $form["email"],
                "password" => Hash::make($form["password"]),

            ])->withoutRelations();

            $statecode = Response::HTTP_CREATED;

            DB::commit();

        } catch (Exception $exception) {

            DB::rollback();
        }

        if ($data) {

            $data->auth = Auth::guard("api")->login($data);

            event(new Registered($data));
        }

        if ($request->wantsJson()) {

            return iresponse($data, $statecode);

        } else {

            return redirect(RouteServiceProvider::HOME);
        }
    }
}
