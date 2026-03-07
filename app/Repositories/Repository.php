<?php

namespace App\Repositories;

use Tripteki\RequestResponseQuery\QueryBuilder;
use Tripteki\RequestResponseQuery\AllowedSort;
use Tripteki\RequestResponseQuery\AllowedFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

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
     * @param array $defaultSorts
     * @param array $defaultFilters
     * @return mixed
     */
    public function accessAll(
        callable $callback,
        $sortables = [],
        $filterables = [],
        $defaultSorts = [],
        $defaultFilters = []
    )
    {
        $content = QueryBuilder::for($callback ());

        if (! empty($sortables)) {
            $content = $content->allowedSorts($sortables);
        }

        if (! empty($defaultSorts)) {
            $content = $content->defaultSort($defaultSorts);
        }

        if (! empty($filterables)) {
            if (! empty($defaultFilters)) {
                $content = $content->allowedFilters(
                    array_map(function (string $key) use ($defaultFilters): AllowedFilter {
                        $default = $defaultFilters[$key] ?? null; return $default !== null ? AllowedFilter::scope($key)->default($default) : AllowedFilter::scope($key);
                    }, $filterables)
                );
            } else {
                $content = $content->allowedFilters($filterables);
            }
        }

        $content = $content->paginate()->appends(request()->query());

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
     */
    public function accessGet(
        callable $callback
    ): Model|Collection|null
    {
        $content = $callback ();

        return $content;
    }

    /**
     * @param callable $callback
     * @return \Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
     */
    public function mutateUpdate(
        callable $callback
    ): Model|Collection|null
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
     * @return \Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
     */
    public function mutateCreate(
        callable $callback
    ): Model|Collection|null
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
     * @return \Illuminate\Database\Eloquent\Model|Illuminate\Database\Eloquent\Collection|null
     */
    public function mutateDelete(
        callable $callback
    ): Model|Collection|null
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
