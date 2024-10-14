<?php

namespace Src\V1\Sample\Services;

use Src\V1\Sample\Contracts\Repositories\SampleContract;
use Src\V1\Common\Services\Service as BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SampleService extends BaseService
{
    /**
     * @var \Src\V1\Sample\Contracts\Repositories\SampleContract
     */
    protected $sampleRepository;

    /**
     * @param \Src\V1\Sample\Contracts\Repositories\SampleContract $sampleRepository
     * @return void
     */
    public function __construct(SampleContract $sampleRepository)
    {
        $this->sampleRepository = $sampleRepository;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($datas)
    {
        return iresponse($this->sampleRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        return iresponse($this->sampleRepository->get($datas["id"]), 200);
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

        return iresponse($this->sampleRepository->update($datas["id"], $datas), 201);
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

        return iresponse($this->sampleRepository->create($datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($datas)
    {
        return iresponse($this->sampleRepository->delete($datas["id"]), 200);
    }
};
