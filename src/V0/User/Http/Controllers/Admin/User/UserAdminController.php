<?php

namespace Src\V0\User\Http\Controllers\Admin\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Tripteki\User\Contracts\Repository\Admin\IUserRepository as IUserAdminRepository;
use Src\V0\User\Imports\Users\UserImport;
use Src\V0\User\Exports\Users\UserExport;
use Src\V0\User\Http\Requests\Admin\Users\UserShowValidation;
use Src\V0\User\Http\Requests\Admin\Users\UserStoreValidation;
use Src\V0\User\Http\Requests\Admin\Users\UserUpdateValidation;
use Src\V0\User\Http\Requests\Admin\Users\UserDestroyValidation;
use Tripteki\Helpers\Http\Requests\FileImportValidation;
use Tripteki\Helpers\Http\Requests\FileExportValidation;
use App\Http\Controllers\Controller as BaseController;

class UserAdminController extends BaseController
{
    /**
     * @var \Tripteki\User\Contracts\Repository\Admin\IUserRepository
     */
    protected $userAdminRepository;

    /**
     * @param \Tripteki\User\Contracts\Repository\Admin\IUserRepository $userAdminRepository
     * @return void
     */
    public function __construct(IUserAdminRepository $userAdminRepository)
    {
        $this->userAdminRepository = $userAdminRepository;
    }

    /**
     * @OA\Get(
     *      path="/admin/users",
     *      tags={"Admin User"},
     *      summary="Index",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="User's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="User's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="User's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="User's Pagination Filter."
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

        $data = $this->userAdminRepository->all();

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/users/{identifier}",
     *      tags={"Admin User"},
     *      summary="Show",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="identifier",
     *          description="User's Identifier."
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
     * @param \App\Http\Requests\Admin\Users\UserShowValidation $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserShowValidation $request, $identifier)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 200;

        $data = $this->userAdminRepository->get($identifier);

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/users",
     *      tags={"Admin User"},
     *      summary="Store",
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
     *          response=422,
     *          description="Unprocessable Entity."
     *      )
     * )
     *
     * @param \App\Http\Requests\Admin\Users\UserStoreValidation $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreValidation $request)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->userAdminRepository->create($form);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Put(
     *      path="/admin/users/{identifier}",
     *      tags={"Admin User"},
     *      summary="Update",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="identifier",
     *          description="User's Identifier."
     *      ),
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
     * @param \App\Http\Requests\Admin\Users\UserUpdateValidation $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateValidation $request, $identifier)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->userAdminRepository->update($identifier, [

            "name" => $form["name"],
            "email" => $form["email"],
            "password" => $form["password"],
        ]);

        if ($data) {

            $statecode = 201;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Delete(
     *      path="/admin/users/{identifier}",
     *      tags={"Admin User"},
     *      summary="Destroy",
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="identifier",
     *          description="User's Identifier."
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
     * @param \App\Http\Requests\Admin\Users\UserDestroyValidation $request
     * @param string $identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserDestroyValidation $request, $identifier)
    {
        $form = $request->validated();
        $data = [];
        $statecode = 202;

        $data = $this->userAdminRepository->delete($identifier);

        if ($data) {

            $statecode = 200;
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Post(
     *      path="/admin/users-import",
     *      tags={"Admin User"},
     *      summary="Import",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="User's File."
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

            $data = Excel::import(new UserImport(), $form["file"], null, \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"]->getClientOriginalExtension() == "xls") {

            $data = Excel::import(new UserImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"]->getClientOriginalExtension() == "xlsx") {

            $data = Excel::import(new UserImport(), $form["file"], null, \Maatwebsite\Excel\Excel::XLSX);
        }

        return iresponse($data, $statecode);
    }

    /**
     * @OA\Get(
     *      path="/admin/users-export",
     *      tags={"Admin User"},
     *      summary="Export",
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="User's File."
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

            $data = Excel::download(new UserExport(), "User.csv", \Maatwebsite\Excel\Excel::CSV);

        } else if ($form["file"] == "xls") {

            $data = Excel::download(new UserExport(), "User.xls", \Maatwebsite\Excel\Excel::XLS);

        } else if ($form["file"] == "xlsx") {

            $data = Excel::download(new UserExport(), "User.xlsx", \Maatwebsite\Excel\Excel::XLSX);
        }

        return $data;
    }
};
