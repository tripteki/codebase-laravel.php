<?php

namespace Src\V0\ACL\Http\Controllers\ACL;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller as BaseController;

class ACLController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/acls",
     *      tags={"ACLs"},
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

        $data = accesses($request->user(), false);

        return iresponse($data, $statecode);
    }
};
