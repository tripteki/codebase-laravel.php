<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Trip Teknologi",
    description: "Trip Teknologi Documentation",
    version: "1.0.0"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    in: "header"
)]
abstract class Controller
{
    //
}
