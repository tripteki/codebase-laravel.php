<?php

namespace Src\V1\Post\Repositories;

use Error;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Repositories\Repository as BaseRepository;
use Src\V1\Post\Http\Resources\PostResource;
use Src\V1\Post\Events\PostShowed;
use Src\V1\Post\Events\PostUpdated;
use Src\V1\Post\Events\PostCreated;
use Src\V1\Post\Events\PostDeleted;
use Src\V1\Post\Contracts\Repositories\PostContract;
use Tripteki\RequestResponseQuery\QueryBuilder;

class PostRepository extends BaseRepository implements PostContract
{
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

        $content = $this->user;
        $content = $content->setRelation("posts",
            QueryBuilder::for($content->posts())->
            with("user")->
            defaultSort("id")->
            allowedSorts([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
            allowedFilters([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
            paginate($limit, [ "*", ], "current_page", $current_page)->appends(empty($querystring) ? request()->query() : $querystringed));
        $content = $content->loadCount("posts");

        return new PostResource($content);
    }

    /**
     * @param int|string $identifier
     * @param array $querystring|[]
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function get($identifier, $querystring = [])
    {
        $content = $this->user->posts()->findOrFail($identifier);

        broadcast(new PostShowed($content = new PostResource($content->load("user"))))->toOthers();

        return $content;
    }

    /**
     * @param int|string $identifier
     * @param array $data
     * @return \Src\V1\Post\Http\Resources\PostResource
     */
    public function update($identifier, $data)
    {
        $content = $this->user->posts()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->update($data);

            DB::commit();

            broadcast(new PostUpdated($content = new PostResource($content->load("user"))))->toOthers();

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

            $content = $this->user->posts()->create($data);

            DB::commit();

            broadcast(new PostCreated($content = new PostResource($content->load("user"))))->toOthers();

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
        $content = $this->user->posts()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->delete();

            DB::commit();

            broadcast(new PostDeleted($content = new PostResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }
};
