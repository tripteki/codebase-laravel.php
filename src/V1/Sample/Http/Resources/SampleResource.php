<?php

namespace Src\V1\Sample\Http\Resources;

use Src\V1\Sample\Models\SampleModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Tripteki\ACL\Contracts\Repository\IACLRepository;

class SampleResource extends JsonResource
{
    /**
     * @var \Tripteki\ACL\Contracts\Repository\IACLRepository
     */
    protected $repository;

    /**
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->repository = app(IACLRepository::class);
        $this->repository->setUser(Auth::user());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            "id" => $this->whenHas("id"),
            "content" => $this->whenHas("content"),
            "created_at" => $this->whenHas("created_at"),
            "updated_at" => $this->whenHas("updated_at"),
            "deleted_at" => $this->whenHas("deleted_at"),
            "user" => $this->whenLoaded("user"),
            "samples" => $this->whenLoaded("samples"),
            "samples_count" => $this->whenCounted("samples"),
            "controls" => $this->repository->owns(SampleModel::class, $this->id),
        ];
    }
};
