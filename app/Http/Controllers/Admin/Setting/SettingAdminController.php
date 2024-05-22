<?php

namespace App\Http\Controllers\Admin\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\Setting\Contracts\Repository\Admin\ISettingRepository as ISettingAdminRepository;
use App\Imports\Settings\SettingImport;
use App\Exports\Settings\SettingExport;
use App\Http\Requests\Admin\Settings\SettingShowValidation;
use App\Http\Requests\Admin\Settings\SettingStoreValidation;
use App\Http\Requests\Admin\Settings\SettingUpdateValidation;
use App\Http\Requests\Admin\Settings\SettingDestroyValidation;
use Tripteki\Helpers\Http\Requests\FileImportValidation;
use Tripteki\Helpers\Http\Requests\FileExportValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class SettingAdminController extends Controller
{
    /**
     * @var \Tripteki\Setting\Contracts\Repository\Admin\ISettingRepository
     */
    protected $settingAdminRepository;

    /**
     * @param \Tripteki\Setting\Contracts\Repository\Admin\ISettingRepository $settingAdminRepository
     * @return void
     */
    public function __construct(ISettingAdminRepository $settingAdminRepository)
    {
        $this->settingAdminRepository = $settingAdminRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/settings",
     *      tags={"Admin Setting"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Setting's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Setting's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Setting's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Setting's Pagination Filter."
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

        $data = $this->settingAdminRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/settings/{key}",
     *      tags={"Admin Setting"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Setting's Key."
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
     * @param \App\Http\Requests\Admin\Settings\SettingShowValidation $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SettingShowValidation $request, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->settingAdminRepository->get($key);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/settings",
     *      tags={"Admin Setting"},
     *      summary="Store",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="key",
     *                      type="string",
     *                      description="Setting's Key."
     *                  ),
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
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\SettingStoreValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SettingStoreValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->settingAdminRepository->create($form);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/admin/settings/{key}",
     *      tags={"Admin Setting"},
     *      summary="Update",
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
     * @param \App\Http\Requests\Admin\Settings\SettingUpdateValidation $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SettingUpdateValidation $request, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->settingAdminRepository->update($key, [ "value" => $form["value"], ]);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/admin/settings/{key}",
     *      tags={"Admin Setting"},
     *      summary="Destroy",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="key",
     *          description="Setting's Key."
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
     * @param \App\Http\Requests\Admin\Settings\SettingDestroyValidation $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SettingDestroyValidation $request, $key)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->settingAdminRepository->delete($key);

        if ($data) {

            $statecode = 200;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/settings-import",
     *      tags={"Admin Setting"},
     *      summary="Import",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Setting's File."
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

            $data = Excel::import(new SettingImport(), $form["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"]->getClientOriginalExtension() == "xls") {

            $data = Excel::import(new SettingImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"]->getClientOriginalExtension() == "xlsx") {

            $data = Excel::import(new SettingImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/settings-export",
     *      tags={"Admin Setting"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Setting's File."
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

            $data = Excel::download(new SettingExport(), "Setting.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new SettingExport(), "Setting.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new SettingExport(), "Setting.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
