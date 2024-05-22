<?php

namespace App\Exceptions;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * @var array<int, string>
     */
    protected $dontFlash = [

        // "current_password",
        // "password",
        // "password_confirmation",
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|mixed
     */
    public function render($request, Throwable $thrower)
    {
        if ($thrower instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {

            $field = app($thrower->getModel())->getKeyName();
            $id = $thrower->getIds();

            $data = [

                $field => [ __("validation.exists", [ "attribute" => Str::lower(Str::headline($field)), ]), ],
            ];

            return iresponse($data, Response::HTTP_UNPROCESSABLE_ENTITY, "Data not found.");
        }

        $response = $thrower;

        return parent::render($request, $response);
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $thrower) {

            //
        });

        //

        $this->renderable(function (\Illuminate\Validation\ValidationException $exception) {

            return iresponse($exception->errors(), Response::HTTP_UNPROCESSABLE_ENTITY, "Data not validated.");
        });

        //

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token is invalid.");
        });

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token has expired.");
        });

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token has blacklisted.");
        });

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "User is not defined.");
        });

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\InvalidClaimException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Claim is invalid.");
        });

        $this->renderable(function (\Tymon\JWTAuth\Exceptions\PayloadException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Payload is invalid.");
        });
    }
}
