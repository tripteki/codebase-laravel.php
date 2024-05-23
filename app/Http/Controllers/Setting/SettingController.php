<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tripteki\Setting\Contracts\Repository\ISettingRepository;
use App\Http\Requests\Settings\SettingUpdateValidation;
use App\Http\Requests\Settings\SettingDestroyValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class SettingController extends Controller
{
    /**
     * @var \Tripteki\Setting\Contracts\Repository\ISettingRepository
     */
    protected $settingRepository;

    /**
     * @param \Tripteki\Setting\Contracts\Repository\ISettingRepository $settingRepository
     * @return void
     */
    public function __construct(ISettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * @OA\Get(
     *      path="/settings",
     *      tags={"Settings"},
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

        $this->settingRepository->setUser($request->user());

        $data = $this->settingRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/settings/{key}",
     *      tags={"Settings"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Setting's Key."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="value",
     *                      type="string",
     *                      description="Setting's Value."
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
     * @param \App\Http\Requests\Settings\SettingUpdateValidation $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SettingUpdateValidation $request, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->settingRepository->setUser($request->user());

        if ($this->settingRepository->getUser()) {

            $data = $this->settingRepository->setup($key, $form["value"]);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/settings/{key}",
     *      tags={"Settings"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Setting's Value."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
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
     * @param \App\Http\Requests\Settings\SettingDestroyValidation $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SettingDestroyValidation $request, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->settingRepository->setUser($request->user());

        if ($this->settingRepository->getUser()) {

            $data = $this->settingRepository->setdown($key);

            if ($data) {

                $statecode = 200;
            }
        }

        return iresponse($data, $statecode);
    }
};
