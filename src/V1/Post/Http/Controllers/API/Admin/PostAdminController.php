<?php

namespace Src\V1\Post\Http\Controllers\API\Admin;

use Src\V1\Post\Repositories\Admin\PostAdminRepository;
use Src\V1\Post\Services\Admin\PostAdminService;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;

class PostAdminController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/v1/admin/master/posts/select",
     *      tags={"Admin Master Posts"},
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
        return (new PostAdminService(new PostAdminRepository()))->select();
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/posts",
     *      tags={"Admin Master Posts"},
     *      summary="Index",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="limit",
     *          description="Post's Pagination Limit."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="current_page",
     *          description="Post's Pagination Current Page."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="order",
     *          description="Post's Pagination Order."
     *      ),
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="filter[]",
     *          description="Post's Pagination Filter."
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
        return (new PostAdminService(new PostAdminRepository()))->index($this->datas());
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/posts/{id}",
     *      tags={"Admin Master Posts"},
     *      summary="Show",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Post's Identifier."
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
        return (new PostAdminService(new PostAdminRepository()))->show($this->datas());
    }

    /**
     * @OA\Put(
     *      path="/v1/admin/master/posts/{id}",
     *      tags={"Admin Master Posts"},
     *      summary="Update",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Post's Identifier."
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="string",
     *                      description="Post's User Identifier."
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                      description="Post's Content."
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
        return (new PostAdminService(new PostAdminRepository()))->update($this->datas());
    }

    /**
     * @OA\Post(
     *      path="/v1/admin/master/posts",
     *      tags={"Admin Master Posts"},
     *      summary="Store",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="user_id",
     *                      type="string",
     *                      description="Post's User Identifier."
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                      description="Post's Content."
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
        return (new PostAdminService(new PostAdminRepository()))->store($this->datas());
    }

    /**
     * @OA\Delete(
     *      path="/v1/admin/master/posts/{id}",
     *      tags={"Admin Master Posts"},
     *      summary="Destroy",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Post's Identifier."
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
        return (new PostAdminService(new PostAdminRepository()))->destroy($this->datas());
    }

    /**
     * @OA\Put(
     *      path="/v1/admin/master/posts/restore/{id}",
     *      tags={"Admin Master Posts"},
     *      summary="Restore",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=true,
     *          in="path",
     *          name="id",
     *          description="Post's Identifier."
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
        return (new PostAdminService(new PostAdminRepository()))->restore($this->datas());
    }

    /**
     * @OA\Post(
     *      path="/v1/admin/master/posts/import",
     *      tags={"Admin Master Posts"},
     *      summary="Import",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                      description="Post's File."
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
        return (new PostAdminService(new PostAdminRepository()))->import($request->validated());
    }

    /**
     * @OA\Get(
     *      path="/v1/admin/master/posts/export",
     *      tags={"Admin Master Posts"},
     *      summary="Export",
     *      security={{ "bearerAuth": {} }},
     *      @OA\Parameter(
     *          required=false,
     *          in="query",
     *          name="file",
     *          schema={"type": "string", "enum": {"csv", "xls", "xlsx"}},
     *          description="Post's File."
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
        return (new PostAdminService(new PostAdminRepository()))->export($request->validated());
    }
};
