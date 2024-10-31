<?php

namespace Src\V1\Sample\Services\Admin;

use Src\V1\Sample\Events\Admin\SampleAdminShowed;
use Src\V1\Sample\Events\Admin\SampleAdminUpdated;
use Src\V1\Sample\Events\Admin\SampleAdminCreated;
use Src\V1\Sample\Events\Admin\SampleAdminActivated;
use Src\V1\Sample\Events\Admin\SampleAdminDeactivated;
use Src\V1\Sample\Imports\Admin\SampleAdminImport;
use Src\V1\Sample\Exports\Admin\SampleAdminExport;
use Src\V1\Sample\Contracts\Repositories\Admin\SampleAdminContract;
use App\Models\User As UserModel;
use Src\V1\Sample\Models\SampleModel;
use Src\V1\Common\Services\Service as BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SampleAdminService extends BaseService
{
    /**
     * @var \Src\V1\Sample\Contracts\Repositories\Admin\SampleAdminContract
     */
    protected $sampleAdminRepository;

    /**
     * @param \Src\V1\Sample\Contracts\Repositories\Admin\SampleAdminContract $sampleAdminRepository
     * @return void
     */
    public function __construct(SampleAdminContract $sampleAdminRepository)
    {
        $this->sampleAdminRepository = $sampleAdminRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function select()
    {
        return $data = iresponse($this->sampleAdminRepository->select(), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($datas)
    {
        return $data = iresponse($this->sampleAdminRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        Validator::make($datas,
        [
            "id" => [ "required", Rule::exists(SampleModel::class), ],
        ])->validate();

        broadcast(
            new SampleAdminShowed(
                $data = iresponse($this->sampleAdminRepository->get($datas["id"]), 200)
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
            "id" => [ "required", Rule::exists(SampleModel::class), ],
            "user_id" => [ "required", Rule::exists(UserModel::class, "id"), ],
            "content" => [ "required", "string", "min:1", "max:280", ],
        ])->validate();

        broadcast(
            new SampleAdminUpdated(
                $data = iresponse($this->sampleAdminRepository->update($datas["id"], $datas), 201)
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
            "user_id" => [ "required", Rule::exists(UserModel::class, "id"), ],
            "content" => [ "required", "string", "min:1", "max:280", ],
        ])->validate();

        broadcast(
            new SampleAdminCreated(
                $data = iresponse($this->sampleAdminRepository->create($datas), 201)
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

        broadcast(
            new SampleAdminDeactivated(
                $data = iresponse($this->sampleAdminRepository->deactivate($datas["id"]), 200)
            )
        )->toOthers();

        return $data;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($datas)
    {
        Validator::make($datas,
        [
            "id" => [ "required", Rule::exists(SampleModel::class)->where(function ($query) {
                return $query->whereNotNull("deleted_at");
            }), ],
        ])->validate();

        broadcast(
            new SampleAdminActivated(
                $data = iresponse($this->sampleAdminRepository->activate($datas["id"]), 200)
            )
        )->toOthers();

        return $data;
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function import($datas)
    {
        return $data = iresponse($this->importable(new SampleAdminImport(), $datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function export($datas)
    {
        return $data = $this->exportable("Sample", new SampleAdminExport(), $datas);
    }
};
