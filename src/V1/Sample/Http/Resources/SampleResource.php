<?php

namespace Src\V1\Sample\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleResource extends JsonResource
{
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
        ];
    }
};
