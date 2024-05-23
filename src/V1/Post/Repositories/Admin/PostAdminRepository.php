<?php

namespace Src\V1\Post\Repositories\Admin;

use Error;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Src\V1\Post\Models\PostModel;
use Src\V1\Post\Http\Resources\PostResource;
use Src\V1\Post\Events\Admin\PostAdminShowed;
use Src\V1\Post\Events\Admin\PostAdminUpdated;
use Src\V1\Post\Events\Admin\PostAdminCreated;
use Src\V1\Post\Events\Admin\PostAdminActivated;
use Src\V1\Post\Events\Admin\PostAdminDeactivated;
use Src\V1\Post\Contracts\Repositories\Admin\PostAdminContract;
use Tripteki\RequestResponseQuery\QueryBuilder;

class PostAdminRepository implements PostAdminContract
{
    /**
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function select()
    {
        $content = PostModel::with("user")->get();

        return PostResource::collection($content);
    }

    /**
     * @param array $querystring|[]
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function all($querystring = [])
    {
        $querystringed =
        [
            "limit" => $querystring["limit"] ?? request()->query("limit", 10),
            "current_page" => $querystring["current_page"] ?? request()->query("current_page", 1),
        ];
        extract($querystringed);

        $content = QueryBuilder::for(PostModel::class)->
        withTrashed()->
        with("user")->
        defaultSort("id")->
        allowedSorts([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
        allowedFilters([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
        paginate($limit, [ "*", ], "current_page", $current_page)->appends(empty($querystring) ? request()->query() : $querystringed);

        return PostResource::collection($content)->response()->getData();
    }

    /**
     * @param int|string $identifier
     * @param array $querystring|[]
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function get($identifier, $querystring = [])
    {
        $content = PostModel::withTrashed()->findOrFail($identifier);

        broadcast(new PostAdminShowed($content = new PostResource($content->load("user"))))->toOthers();

        return $content;
    }

    /**
     * @param int|string $identifier
     * @param array $data
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function update($identifier, $data)
    {
        $content = PostModel::withTrashed()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->update($data);

            DB::commit();

            broadcast(new PostAdminUpdated($content = new PostResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param array $data
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function create($data)
    {
        $content = null;

        DB::beginTransaction();

        try {

            $content = PostModel::create($data);

            DB::commit();

            broadcast(new PostAdminCreated($content = new PostResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param int|string $identifier
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function delete($identifier)
    {
        return $this->deactivate($identifier);
    }

    /**
     * @param int|string $identifier
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function activate($identifier)
    {
        $content = PostModel::onlyTrashed()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->restore();

            DB::commit();

            broadcast(new PostAdminActivated($content = new PostResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param int|string $identifier
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function deactivate($identifier)
    {
        $content = PostModel::findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->delete();

            DB::commit();

            broadcast(new PostAdminDeactivated($content = new PostResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }
};
