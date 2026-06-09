<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: "Unauthorized",
    description: "Unauthorized.",
    content: new OA\JsonContent(example: ["detail" => "string"]),
)]
#[OA\Response(
    response: "Forbidden",
    description: "Forbidden.",
    content: new OA\JsonContent(example: ["detail" => "string"]),
)]
#[OA\Response(
    response: "Unvalidated",
    description: "Validation Error.",
    content: new OA\JsonContent(
        example: [
            "detail" => [
                [
                    "type" => "value_error",
                    "loc" => ["body", "field"],
                    "msg" => "string",
                    "input" => [],
                    "ctx" => ["error" => "string"],
                ],
            ],
        ],
    ),
)]
#[OA\Response(
    response: "UserMeSuccess",
    description: "Success.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "id", type: "string"),
            new OA\Property(property: "name", type: "string"),
            new OA\Property(property: "email", type: "string", format: "email"),
            new OA\Property(property: "email_verified_at", type: "string", format: "date-time", nullable: true),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time"),
            new OA\Property(
                property: "profile",
                properties: [
                    new OA\Property(property: "full_name", type: "string", nullable: true),
                    new OA\Property(property: "avatar", type: "string", nullable: true),
                    new OA\Property(property: "avatar_url", type: "string", nullable: true),
                    new OA\Property(
                        property: "interests",
                        type: "array",
                        items: new OA\Items(type: "string"),
                    ),
                ],
                type: "object",
                nullable: true,
            ),
        ],
    ),
)]
#[OA\Response(
    response: "UserAccessSuccess",
    description: "Success.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "permissions",
                type: "array",
                items: new OA\Items(type: "string"),
            ),
            new OA\Property(
                property: "roles",
                type: "array",
                items: new OA\Items(type: "string"),
            ),
        ],
    ),
)]
#[OA\Response(
    response: "ProfileInterestsSuccess",
    description: "Success.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: "data",
                type: "array",
                items: new OA\Items(type: "string"),
            ),
        ],
    ),
)]
#[OA\Response(
    response: "OffsetPaginationSuccess",
    description: "Success.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "totalPage", type: "integer"),
            new OA\Property(property: "perPage", type: "integer"),
            new OA\Property(property: "currentPage", type: "integer"),
            new OA\Property(property: "nextPage", type: "integer", nullable: true),
            new OA\Property(property: "previousPage", type: "integer", nullable: true),
            new OA\Property(property: "firstPage", type: "integer"),
            new OA\Property(property: "lastPage", type: "integer"),
            new OA\Property(property: "data", type: "array", items: new OA\Items(type: "object")),
        ],
    ),
)]
#[OA\Response(
    response: "AuthTokenSuccess",
    description: "Success.",
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "accessTokenTtl", type: "integer"),
            new OA\Property(property: "refreshTokenTtl", type: "integer"),
            new OA\Property(property: "accessToken", type: "string"),
            new OA\Property(property: "refreshToken", type: "string"),
        ],
    ),
)]
class OpenApiResponses
{
    //
}
