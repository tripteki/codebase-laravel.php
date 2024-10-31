<?php

namespace Src\V1\Sample\Services;

use Src\V1\Sample\Events\SampleShowed;
use Src\V1\Sample\Events\SampleUpdated;
use Src\V1\Sample\Events\SampleCreated;
use Src\V1\Sample\Events\SampleDeleted;
use Src\V1\Sample\Contracts\Repositories\SampleContract;
use Src\V1\Sample\Models\SampleModel;
use Src\V1\Common\Services\Service as BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;

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
        return $data = iresponse($this->sampleRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        Validator::make($datas,
        [
            "id" => [ "required", Rule::exists(SampleModel::class)->where(function ($query) {
                return $query->whereNull("deleted_at");
            }), ],
        ])->validate();

        if (! App::runningInConsole()) {
            Gate::authorize("view", SampleModel::findOrFail($datas["id"]));
        }

        broadcast(
            new SampleShowed(
                $data = iresponse($this->sampleRepository->get($datas["id"]), 200)
            )
        )->toOthers();

        return $data;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($datas)
    {
        Validator::make($datas,
        [
            "id" => [ "required", Rule::exists(SampleModel::class)->where(function ($query) {
                return $query->whereNull("deleted_at");
            }), ],
            "content" => [ "required", "string", "min:1", "max:280", ],
        ])->validate();

        if (! App::runningInConsole()) {
            Gate::authorize("update", SampleModel::findOrFail($datas["id"]));
        }

        broadcast(
            new SampleUpdated(
                $data = iresponse($this->sampleRepository->update($datas["id"], $datas), 201)
            )
        )->toOthers();

        return $data;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($datas)
    {
        Validator::make($datas,
        [
            "content" => [ "required", "string", "min:1", "max:280", ],
        ])->validate();

        if (! App::runningInConsole()) {
            Gate::authorize("create", SampleModel::class);
        }

        broadcast(
            new SampleCreated(
                $data = iresponse($this->sampleRepository->create($datas), 201)
            )
        )->toOthers();

        return $data;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($datas)
    {
        Validator::make($datas,
        [
            "id" => [ "required", Rule::exists(SampleModel::class)->where(function ($query) {
                return $query->whereNull("deleted_at");
            }), ],
        ])->validate();

        if (! App::runningInConsole()) {
            Gate::authorize("delete", SampleModel::findOrFail($datas["id"]));
        }

        broadcast(
            new SampleDeleted(
                $data = iresponse($this->sampleRepository->delete($datas["id"]), 200)
            )
        )->toOthers();

        return $data;
    }
};
