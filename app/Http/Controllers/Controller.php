<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      title="Application",
 *      version="1.0"
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 */
class Controller extends BaseController
{
    use Authorizable, AuthorizesRequests;

    /**
     * @return array
     */
    protected function datas()
    {
        return $datas = array_merge(Route::getCurrentRoute()->parameters(), request()->all());
    }
}
