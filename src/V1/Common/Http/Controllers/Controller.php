<?php

namespace Src\V1\Common\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller as BaseController;

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
