<?php

namespace App\Exceptions;

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
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $thrower) {

            //
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token is invalid.");
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token has expired.");
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Token has blacklisted.");
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "User is not defined.");
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\InvalidClaimException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Claim is invalid.");
        });

        $this->reportable(function (\Tymon\JWTAuth\Exceptions\PayloadException $exception) {

            return iresponse([], Response::HTTP_UNAUTHORIZED, "Payload is invalid.");
        });
    }
}
