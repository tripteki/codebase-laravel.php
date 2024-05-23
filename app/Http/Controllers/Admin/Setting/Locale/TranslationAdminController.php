<?php

namespace App\Http\Controllers\Admin\Setting\Locale;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleTranslationRepository;
use App\Imports\Settings\Locales\TranslationImport;
use App\Exports\Settings\Locales\TranslationExport;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationIndexValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationShowValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationStoreValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationUpdateValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationDestroyValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationFileImportValidation as FileImportValidation;
use App\Http\Requests\Admin\Settings\Locales\Translations\TranslationFileExportValidation as FileExportValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class TranslationAdminController extends Controller
{
    /**
     * @var \Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleTranslationRepository
     */
    protected $translationRepository;

    /**
     * @param \Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleTranslationRepository $translationRepository
     * @return void
     */
    public function __construct(ISettingLocaleTranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages/{code}/translations",
     *      tags={"Admin Locale Translation"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Locale Translation's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Locale Translation's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Locale Translation's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Locale Translation's Pagination Filter."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationIndexValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(TranslationIndexValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->translationRepository->all($code);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages/{code}/translations/{key}",
     *      tags={"Admin Locale Translation"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Locale Language's Key."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not Found."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationShowValidation $request
     * @param string $code
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TranslationShowValidation $request, $code, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->translationRepository->get($code, $key);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/locales/languages/{code}/translations",
     *      tags={"Admin Locale Translation"},
     *      summary="Store",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="key",
     *                      type="string",
     *                      description="Locale Translation's Key."
     *                  ),
     *                  @OA\Property(
     *                      property="translate",
     *                      type="string",
     *                      description="Locale Translation's Translate."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationStoreValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TranslationStoreValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->translationRepository->create($code, $form);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/admin/locales/languages/{code}/translations/{key}",
     *      tags={"Admin Locale Translation"},
     *      summary="Update",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Locale Language's Key."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="key",
     *                      type="string",
     *                      description="Locale Translation's Key."
     *                  ),
     *                  @OA\Property(
     *                      property="translate",
     *                      type="string",
     *                      description="Locale Translation's Translate."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationUpdateValidation $request
     * @param string $code
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TranslationUpdateValidation $request, $code, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->translationRepository->update($code, $key, [ "key" => $form["key"], "translate" => $form["translate"], ]);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/admin/locales/languages/{code}/translations/{key}",
     *      tags={"Admin Locale Translation"},
     *      summary="Destroy",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Locale Language's Key."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationDestroyValidation $request
     * @param string $code
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(TranslationDestroyValidation $request, $code, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->translationRepository->delete($code, $key);

        if ($data) {

            $statecode = 200;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/locales/languages/{code}/translations-import",
     *      tags={"Admin Locale Translation"},
     *      summary="Import",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Locale Translation's File."
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationFileImportValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(FileImportValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        if ($form["file"]->getClientOriginalExtension() == "csv" || $form["file"]->getClientOriginalExtension() == "txt") {

            $data = Excel::import(new TranslationImport($code), $form["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"]->getClientOriginalExtension() == "xls") {

            $data = Excel::import(new TranslationImport($code), $form["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"]->getClientOriginalExtension() == "xlsx") {

            $data = Excel::import(new TranslationImport($code), $form["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages/{code}/translations-export",
     *      tags={"Admin Locale Translation"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Locale Translation's File."
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success."
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\Locales\Translations\TranslationFileExportValidation $request
     * @param string $code
     * @return mixed
     */
    public function export(FileExportValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        if ($form["file"] == "csv") {

            $data = Excel::download(new TranslationExport($code), "Translation.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new TranslationExport($code), "Translation.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new TranslationExport($code), "Translation.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
