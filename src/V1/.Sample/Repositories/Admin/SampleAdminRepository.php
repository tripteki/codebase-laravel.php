<?php

namespace Src\V1\Sample\Repositories\Admin;

use Error;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Src\V1\Sample\Models\SampleModel;
use Src\V1\Sample\Http\Resources\SampleResource;
use Src\V1\Sample\Events\Admin\SampleAdminShowed;
use Src\V1\Sample\Events\Admin\SampleAdminUpdated;
use Src\V1\Sample\Events\Admin\SampleAdminCreated;
use Src\V1\Sample\Events\Admin\SampleAdminActivated;
use Src\V1\Sample\Events\Admin\SampleAdminDeactivated;
use Src\V1\Sample\Contracts\Repositories\Admin\SampleAdminContract;
use Tripteki\RequestResponseQuery\QueryBuilder;

class SampleAdminRepository implements SampleAdminContract
{
    /**
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function select()
    {
        $content = SampleModel::with("user")->get();

        return SampleResource::collection($content);
    }

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

        $content = QueryBuilder::for(SampleModel::class)->
        withTrashed()->
        with("user")->
        defaultSort("id")->
        allowedSorts([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
        allowedFilters([ "id", "content", "created_at", "updated_at", "deleted_at", ])->
        paginate($limit, [ "*", ], "current_page", $current_page)->appends(empty($querystring) ? request()->query() : $querystringed);

        return SampleResource::collection($content)->response()->getData();
    }

    /**
     * @param int|string $identifier
     * @param array $querystring|[]
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function get($identifier, $querystring = [])
    {
        $content = SampleModel::withTrashed()->findOrFail($identifier);

        broadcast(new SampleAdminShowed($content = new SampleResource($content->load("user"))))->toOthers();

        return $content;
    }

    /**
     * @param int|string $identifier
     * @param array $data
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function update($identifier, $data)
    {
        $content = SampleModel::withTrashed()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->update($data);

            DB::commit();

            broadcast(new SampleAdminUpdated($content = new SampleResource($content->load("user"))))->toOthers();

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

            $content = SampleModel::create($data);

            DB::commit();

            broadcast(new SampleAdminCreated($content = new SampleResource($content->load("user"))))->toOthers();

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
        return $this->deactivate($identifier);
    }

    /**
     * @param int|string $identifier
     * @return \Src\V1\Sample\Http\Resources\SampleResource
     */
    public function activate($identifier)
    {
        $content = SampleModel::onlyTrashed()->findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->restore();

            DB::commit();

            broadcast(new SampleAdminActivated($content = new SampleResource($content->load("user"))))->toOthers();

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
    public function deactivate($identifier)
    {
        $content = SampleModel::findOrFail($identifier);

        DB::beginTransaction();

        try {

            $content->delete();

            DB::commit();

            broadcast(new SampleAdminDeactivated($content = new SampleResource($content->load("user"))))->toOthers();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }
};
