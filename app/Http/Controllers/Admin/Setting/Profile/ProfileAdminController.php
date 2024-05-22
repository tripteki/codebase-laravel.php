<?php

namespace App\Http\Controllers\Admin\Setting\Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\SettingProfile\Contracts\Repository\Admin\ISettingProfileEnvironmentRepository;
use App\Imports\Settings\Profiles\ProfileImport;
use App\Exports\Settings\Profiles\ProfileExport;
use App\Http\Requests\Admin\Settings\Profiles\ProfileShowValidation;
use App\Http\Requests\Admin\Settings\Profiles\ProfileStoreValidation;
use App\Http\Requests\Admin\Settings\Profiles\ProfileUpdateValidation;
use App\Http\Requests\Admin\Settings\Profiles\ProfileDestroyValidation;
use Tripteki\Helpers\Http\Requests\FileImportValidation;
use Tripteki\Helpers\Http\Requests\FileExportValidation;
use Tripteki\Helpers\Http\Controllers\Controller;

class ProfileAdminController extends Controller
{
    /**
     * @var \Tripteki\SettingProfile\Contracts\Repository\Admin\ISettingProfileEnvironmentRepository
     */
    protected $profileAdminRepository;

    /**
     * @param \Tripteki\SettingProfile\Contracts\Repository\Admin\ISettingProfileEnvironmentRepository $profileAdminRepository
     * @return void
     */
    public function __construct(ISettingProfileEnvironmentRepository $profileAdminRepository)
    {
        $this->profileAdminRepository = $profileAdminRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/profiles",
     *      tags={"Admin Profile"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Profile's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Profile's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Profile's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Profile's Pagination Filter."
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

        $data = $this->profileAdminRepository->all();

        return iresponse($data->resource, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/profiles/{variable}",
     *      tags={"Admin Profile"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="variable",
     *          description="Profile's Variable."
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
     * @param \App\Http\Requests\Admin\Settings\Profiles\ProfileShowValidation $request
     * @param string $variable
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProfileShowValidation $request, $variable)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->profileAdminRepository->get($variable);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/profiles",
     *      tags={"Admin Profile"},
     *      summary="Store",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="variable",
     *                      type="string",
     *                      description="Profile's Variable."
     *                  ),
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
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Settings\Profiles\ProfileStoreValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProfileStoreValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->profileAdminRepository->create($form);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/admin/profiles/{variable}",
     *      tags={"Admin Profile"},
     *      summary="Update",
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
     * @param \App\Http\Requests\Admin\Settings\Profiles\ProfileUpdateValidation $request
     * @param string $variable
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProfileUpdateValidation $request, $variable)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->profileAdminRepository->update($variable, [ "value" => $form["value"], ]);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/admin/profiles/{variable}",
     *      tags={"Admin Profile"},
     *      summary="Destroy",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="variable",
     *          description="Profile's Variable."
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
     * @param \App\Http\Requests\Admin\Settings\Profiles\ProfileDestroyValidation $request
     * @param string $variable
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProfileDestroyValidation $request, $variable)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->profileAdminRepository->delete($variable);

        if ($data) {

            $statecode = 200;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/profiles-import",
     *      tags={"Admin Profile"},
     *      summary="Import",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Profile's File."
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

            $data = Excel::import(new ProfileImport(), $form["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"]->getClientOriginalExtension() == "xls") {

            $data = Excel::import(new ProfileImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"]->getClientOriginalExtension() == "xlsx") {

            $data = Excel::import(new ProfileImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/profiles-export",
     *      tags={"Admin Profile"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Profile's File."
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

            $data = Excel::download(new ProfileExport(), "Profile.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new ProfileExport(), "Profile.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new ProfileExport(), "Profile.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
