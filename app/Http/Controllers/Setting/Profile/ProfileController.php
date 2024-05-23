<?php

namespace App\Http\Controllers\Setting\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tripteki\SettingProfile\Contracts\Repository\ISettingProfileRepository;
use App\Http\Requests\Settings\Profiles\ProfileUpdateValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * @var \Tripteki\SettingProfile\Contracts\Repository\ISettingProfileRepository
     */
    protected $profileRepository;

    /**
     * @param \Tripteki\SettingProfile\Contracts\Repository\ISettingProfileRepository $profileRepository
     * @return void
     */
    public function __construct(ISettingProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    /**
     * @OA\Get(
     *      path="/profiles",
     *      tags={"Profiles"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = [];
        $statecode = 200;

        $this->profileRepository->setUser($request->user());

        $data = $this->profileRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/profiles/{variable}",
     *      tags={"Profiles"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="variable",
     *          description="Profile's Variable."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="value",
     *                      type="string",
     *                      description="Profile's Value."
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
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Settings\Profiles\ProfileUpdateValidation $request
     * @param string $variable
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProfileUpdateValidation $request, $variable)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->profileRepository->setUser($request->user());

        if ($this->profileRepository->getUser()) {

            $data = $this->profileRepository->update($variable, $form["value"]);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }
};
