<?php

namespace Src\V1\Post\Services\Admin;

use App\Models\User As UserModel;
use Src\V1\Post\Imports\Admin\PostAdminImport;
use Src\V1\Post\Exports\Admin\PostAdminExport;
use Src\V1\Post\Contracts\Repositories\Admin\PostAdminContract;
use App\Services\Service as BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PostAdminService extends BaseService
{
    /**
     * @var \Src\V1\Post\Contracts\Repositories\Admin\PostAdminContract
     */
    protected $postAdminRepository;

    /**
     * @param \Src\V1\Post\Contracts\Repositories\Admin\PostAdminContract $postAdminRepository
     * @return void
     */
    public function __construct(PostAdminContract $postAdminRepository)
    {
        $this->postAdminRepository = $postAdminRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function select()
    {
        return iresponse($this->postAdminRepository->select(), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($datas)
    {
        return iresponse($this->postAdminRepository->all($datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($datas)
    {
        return iresponse($this->postAdminRepository->get($datas["id"]), 200);
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

        return iresponse($this->postAdminRepository->update($datas["id"], $datas), 201);
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

        return iresponse($this->postAdminRepository->create($datas), 201);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($datas)
    {
        return iresponse($this->postAdminRepository->delete($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($datas)
    {
        return iresponse($this->postAdminRepository->activate($datas["id"]), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function import($datas)
    {
        return iresponse($this->importable(new PostAdminImport(), $datas), 200);
    }

    /**
     * @param array $datas
     * @return \Illuminate\Http\JsonResponse
     */
    public function export($datas)
    {
        return $this->exportable("Post", new PostAdminExport(), $datas);
    }
};
