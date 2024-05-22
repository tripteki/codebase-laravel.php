<?php

namespace App\Http\Controllers\Admin\Setting\Locale;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleLanguageRepository;
use App\Imports\Settings\Locales\LanguageImport;
use App\Exports\Settings\Locales\LanguageExport;
use App\Http\Requests\Admin\Settings\Locales\Languages\LanguageShowValidation;
use App\Http\Requests\Admin\Settings\Locales\Languages\LanguageStoreValidation;
use App\Http\Requests\Admin\Settings\Locales\Languages\LanguageUpdateValidation;
use App\Http\Requests\Admin\Settings\Locales\Languages\LanguageDestroyValidation;
use Tripteki\Helpers\Http\Requests\FileImportValidation;
use Tripteki\Helpers\Http\Requests\FileExportValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class LanguageAdminController extends Controller
{
    /**
     * @var \Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleLanguageRepository
     */
    protected $languageRepository;

    /**
     * @param \Tripteki\SettingLocale\Contracts\Repository\Admin\ISettingLocaleLanguageRepository $languageRepository
     * @return void
     */
    public function __construct(ISettingLocaleLanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages",
     *      tags={"Admin Locale Language"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Locale Language's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Locale Language's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Locale Language's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Locale Language's Pagination Filter."
     *      ),
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

        $data = $this->languageRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages/{code}",
     *      tags={"Admin Locale Language"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Languages\LanguageShowValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(LanguageShowValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->languageRepository->get($code);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/locales/languages",
     *      tags={"Admin Locale Language"},
     *      summary="Store",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="code",
     *                      type="string",
     *                      description="Locale Language's Code."
     *                  ),
     *                  @OA\Property(
     *                      property="locale",
     *                      type="string",
     *                      description="Locale Language's Locale."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Languages\LanguageStoreValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LanguageStoreValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->languageRepository->create($form);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/admin/locales/languages/{code}",
     *      tags={"Admin Locale Language"},
     *      summary="Update",
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
     *                      property="locale",
     *                      type="string",
     *                      description="Locale Language's Locale."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Languages\LanguageUpdateValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(LanguageUpdateValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->languageRepository->update($code, [ "locale" => $form["locale"], ]);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/admin/locales/languages/{code}",
     *      tags={"Admin Locale Language"},
     *      summary="Destroy",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="code",
     *          description="Locale Language's Code."
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
     * @param \App\Http\Requests\Admin\Settings\Locales\Languages\LanguageDestroyValidation $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(LanguageDestroyValidation $request, $code)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->languageRepository->delete($code);

        if ($data) {

            $statecode = 200;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/locales/languages-import",
     *      tags={"Admin Locale Language"},
     *      summary="Import",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Locale Language's File."
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
     * @param \Tripteki\Helpers\Http\Requests\FileImportValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(FileImportValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        if ($form["file"]->getClientOriginalExtension() == "csv" || $form["file"]->getClientOriginalExtension() == "txt") {

            $data = Excel::import(new LanguageImport(), $form["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"]->getClientOriginalExtension() == "xls") {

            $data = Excel::import(new LanguageImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"]->getClientOriginalExtension() == "xlsx") {

            $data = Excel::import(new LanguageImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/locales/languages-export",
     *      tags={"Admin Locale Language"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Locale Language's File."
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
     * @param \Tripteki\Helpers\Http\Requests\FileExportValidation $request
     * @return mixed
     */
    public function export(FileExportValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        if ($form["file"] == "csv") {

            $data = Excel::download(new LanguageExport(), "Language.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new LanguageExport(), "Language.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new LanguageExport(), "Language.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
