<?php

namespace Src\V1\Sample\Services\Admin;

use App\Models\User As UserModel;
use Src\V1\Sample\Imports\Admin\SampleAdminImport;
use Src\V1\Sample\Exports\Admin\SampleAdminExport;
use Src\V1\Sample\Contracts\Repositories\Admin\SampleAdminContract;
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
        return iresponse($this->sampleAdminRepository->select(), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($datas)
    {
        return iresponse($this->sampleAdminRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        return iresponse($this->sampleAdminRepository->get($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($datas)
    {
        $validator = Validator::make($datas,
        [
            "user_id" => [ "required", Rule::exists(UserModel::class, "id"), ],
            "content" => [ "required", "string", "min:1", "max:280", ],
        ]);

        $validator->validate();

        return iresponse($this->sampleAdminRepository->update($datas["id"], $datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($datas)
    {
        $validator = Validator::make($datas,
        [
            "user_id" => [ "required", Rule::exists(UserModel::class, "id"), ],
            "content" => [ "required", "string", "min:1", "max:280", ],
        ]);

        $validator->validate();

        return iresponse($this->sampleAdminRepository->create($datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($datas)
    {
        return iresponse($this->sampleAdminRepository->delete($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($datas)
    {
        return iresponse($this->sampleAdminRepository->activate($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function import($datas)
    {
        return iresponse($this->importable(new SampleAdminImport(), $datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function export($datas)
    {
        return $this->exportable("Sample", new SampleAdminExport(), $datas);
    }
};
