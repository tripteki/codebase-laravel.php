<?php

namespace Src\V1\Post\Http\Controllers\API;

use Src\V1\Post\Repositories\PostRepository;
use Src\V1\Post\Services\PostService;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;

class PostController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/v1/posts",
     *      tags={"Posts"},
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
        $repository = new PostRepository();
        $repository->setUser($request->user());
        $service = new PostService($repository);

        return $service->index($this->datas());
    }

    /**
     * @OA\Get(
     *      path="/v1/posts/{id}",
     *      tags={"Posts"},
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
        $repository = new PostRepository();
        $repository->setUser($request->user());
        $service = new PostService($repository);

        return $service->show($this->datas());
    }

    /**
     * @OA\Put(
     *      path="/v1/posts/{id}",
     *      tags={"Posts"},
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
        $repository = new PostRepository();
        $repository->setUser($request->user());
        $service = new PostService($repository);

        return $service->update($this->datas());
    }

    /**
     * @OA\Post(
     *      path="/v1/posts",
     *      tags={"Posts"},
     *      summary="Store",
     *      security={{ "bearerAuth": {} }},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
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
        $repository = new PostRepository();
        $repository->setUser($request->user());
        $service = new PostService($repository);

        return $service->store($this->datas());
    }

    /**
     * @OA\Delete(
     *      path="/v1/posts/{id}",
     *      tags={"Posts"},
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
        $repository = new PostRepository();
        $repository->setUser($request->user());
        $service = new PostService($repository);

        return $service->destroy($this->datas());
    }
};
