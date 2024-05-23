<?php

namespace Src\V1\Post\Services;

use Src\V1\Post\Contracts\Repositories\PostContract;
use App\Services\Service as BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PostService extends BaseService
{
    /**
     * @var \Src\V1\Post\Contracts\Repositories\PostContract
     */
    protected $postRepository;

    /**
     * @param \Src\V1\Post\Contracts\Repositories\PostContract $postRepository
     * @return void
     */
    public function __construct(PostContract $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($datas)
    {
        return iresponse($this->postRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        return iresponse($this->postRepository->get($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($datas)
    {
        $validator = Validator::make($datas,
        [
            "content" => [ "required", "string", "min:1", "max:280", ],
        ]);

        $validator->validate();

        return iresponse($this->postRepository->update($datas["id"], $datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($datas)
    {
        $validator = Validator::make($datas,
        [
            "content" => [ "required", "string", "min:1", "max:280", ],
        ]);

        $validator->validate();

        return iresponse($this->postRepository->create($datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($datas)
    {
        return iresponse($this->postRepository->delete($datas["id"]), 200);
    }
};
