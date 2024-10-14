<?php

namespace Src\V1\Sample\Http\Controllers\API\Admin;

use Src\V1\Sample\Repositories\Admin\SampleAdminRepository;
use Src\V1\Sample\Services\Admin\SampleAdminService;
use Src\V1\Common\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;

class SampleAdminController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/v1/admin/master/samples/select",
     *      tags={"Admin Master Samples"},
     *      summary="Select",
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
    public function select(Request $request)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->select();
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/samples",
     *      tags={"Admin Master Samples"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Sample's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Sample's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Sample's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Sample's Pagination Filter."
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
        return (new SampleAdminService(new SampleAdminRepository()))->index($this->datas());
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/samples/{id}",
     *      tags={"Admin Master Samples"},
     *      summary="Show",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Sample's Identifier."
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
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->show($this->datas());
    }

    /**
     * @OA\Put(
     *      path="/v1/admin/master/samples/{id}",
     *      tags={"Admin Master Samples"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Sample's Identifier."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="string",
     *                      description="Sample's User Identifier."
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                      description="Sample's Content."
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
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->update($this->datas());
    }

    /**
     * @OA\Post(
     *      path="/v1/admin/master/samples",
     *      tags={"Admin Master Samples"},
     *      summary="Store",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="string",
     *                      description="Sample's User Identifier."
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                      description="Sample's Content."
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->store($this->datas());
    }

    /**
     * @OA\Delete(
     *      path="/v1/admin/master/samples/{id}",
     *      tags={"Admin Master Samples"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Sample's Identifier."
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
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->destroy($this->datas());
    }

    /**
     * @OA\Put(
     *      path="/v1/admin/master/samples/restore/{id}",
     *      tags={"Admin Master Samples"},
     *      summary="Restore",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Sample's Identifier."
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
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, $id)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->restore($this->datas());
    }

    /**
     * @OA\Post(
     *      path="/v1/admin/master/samples/import",
     *      tags={"Admin Master Samples"},
     *      summary="Import",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Sample's File."
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
    public function import(\Tripteki\Helpers\Http\Requests\FileImportValidation $request)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->import($request->validated());
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/samples/export",
     *      tags={"Admin Master Samples"},
     *      summary="Export",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Sample's File."
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
    public function export(\Tripteki\Helpers\Http\Requests\FileExportValidation $request)
    {
        return (new SampleAdminService(new SampleAdminRepository()))->export($request->validated());
    }
};
