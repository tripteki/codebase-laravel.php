<?php

namespace Src\V1\Sample\Repositories;

use Error;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Src\V1\Common\Repositories\Repository as BaseRepository;
use Src\V1\Sample\Http\Resources\SampleResource;
use Src\V1\Sample\Events\SampleShowed;
use Src\V1\Sample\Events\SampleUpdated;
use Src\V1\Sample\Events\SampleCreated;
use Src\V1\Sample\Events\SampleDeleted;
use Src\V1\Sample\Contracts\Repositories\SampleContract;
use Tripteki\RequestResponseQuery\QueryBuilder;

class SampleRepository extends BaseRepository implements SampleContract
{
    /**
     * @param array $querystring|[]
     * @return \Src\V1\Sample\Http\Resources\SampleResource
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
        $content = $content->setRelation("samples",
            QueryBuilder::for($content->samples())->
            with("user")->
            defaultSort("id")->
            allowedSorts([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
            allowedFilters([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
            paginate($limit, [ "*", ], "current_page", $current_page)->appends(empty($querystring) ? request()->query() : $querystringed));
        $content = $content->loadCount("samples");

        return new SampleResource($content);
    }

    /**
     * @param int|string $identifier
     * @param array $querystring|[]
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function get($identifier, $querystring = [])
    {
        $content = $this->user->samples()->findOrFail($identifier);

        broadcast(new SampleShowed($content = new SampleResource($content->load("user"))))->toOthers();

        return $content;
    }

    /**
     * @param int|string $identifier
     * @param array $data
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function update($identifier, $data)
    {
        $content = $this->user->samples()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->update($data);

            DB::commit();

            broadcast(new SampleUpdated($content = new SampleResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param array $data
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function create($data)
    {
        $content = null;

        DB::beginTransaction();

        try {

            $content = $this->user->samples()->create($data);

            DB::commit();

            broadcast(new SampleCreated($content = new SampleResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param int|string $identifier
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function delete($identifier)
    {
        $content = $this->user->samples()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->delete();

            DB::commit();

            broadcast(new SampleDeleted($content = new SampleResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }
};
