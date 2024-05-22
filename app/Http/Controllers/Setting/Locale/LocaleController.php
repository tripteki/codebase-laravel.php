<?php

namespace App\Http\Controllers\Setting\Locale;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Tripteki\SettingLocale\Contracts\Repository\ISettingLocaleRepository;
use App\Http\Requests\Settings\Locales\LocaleUpdateValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class LocaleController extends Controller
{
    /**
     * @var \Tripteki\SettingLocale\Contracts\Repository\ISettingLocaleRepository
     */
    protected $localeRepository;

    /**
     * @param \Tripteki\SettingLocale\Contracts\Repository\ISettingLocaleRepository $localeRepository
     * @return void
     */
    public function __construct(ISettingLocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @OA\Get(
     *      path="/locales",
     *      tags={"Locales"},
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

        $data = [

            "code" => locale(),
        ];

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/locales",
     *      tags={"Locales"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      description="Locale's Code."
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
     * @param \App\Http\Requests\Settings\Locales\LocaleUpdateValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LocaleUpdateValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $this->localeRepository->setUser($request->user());

        if ($this->localeRepository->getUser()) {

            $data = $this->localeRepository->setLocale($form["code"]);

            if ($data) {

                $statecode = 201;
            }
        }

        return iresponse($data, $statecode);
    }
};
