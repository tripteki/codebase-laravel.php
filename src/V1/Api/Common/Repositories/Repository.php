<?php

namespace Src\V1\Api\Common\Repositories;

use Tripteki\RequestResponseQuery\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

abstract class Repository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected Model $user;

    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return void
     */
    public function setUser(Model $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getUser(): Model
    {
        return $this->user ?? Auth::user() ?? throw new ModelNotFoundException('User not defined.');
    }

    /**
     * @param callable $callback
     * @param array $sortables
     * @param array $filterables
     * @return mixed
     */
    public function accessAll(
        callable $callback,
        $sortables = [],
        $filterables = []
    )
    {
        $content = QueryBuilder::for($callback ())->
        allowedSorts($sortables)->
        allowedFilters($filterables)->
        paginate()->appends(request()->query());

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function accessGet(
        callable $callback
    ): ?Model
    {
        $content = $callback ();

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function mutateUpdate(
        callable $callback
    ): ?Model
    {
        $content = null;

        DB::beginTransaction();

        try {

            $content = $callback ();

            DB::commit();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function mutateCreate(
        callable $callback
    ): ?Model
    {
        $content = null;

        DB::beginTransaction();

        try {

            $content = $callback ();

            DB::commit();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function mutateDelete(
        callable $callback
    ): ?Model
    {
        $content = null;

        DB::beginTransaction();

        try {

            $content = $callback ();

            DB::commit();

        } catch (Exception $exception) {

            DB::rollback();

            Log::info($exception->getMessage());
        }

        return $content;
    }
}
