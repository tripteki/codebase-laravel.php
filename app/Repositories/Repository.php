<?php

namespace App\Repositories;

use App\Support\QueryParser;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
            $content = $content->allowedSorts(...$sortables);
        }

        if (! empty($defaultSorts)) {
            $content = $content->defaultSort(...$defaultSorts);
        }

        if (! empty($filterables)) {
            if (! empty($defaultFilters)) {
                $content = $content->allowedFilters(
                    ...array_map(function (string $key) use ($defaultFilters): AllowedFilter {
                        $default = $defaultFilters[$key] ?? null;

                        return $default !== null
                            ? AllowedFilter::scope($key)->default($default)
                            : AllowedFilter::scope($key);
                    }, $filterables)
                );
            } else {
                $content = $content->allowedFilters(...$filterables);
            }
        }

        [ $currentPage, $perPage, ] = $this->resolvePaginationParams();

        $content = $content
            ->paginate(perPage: $perPage, page: $currentPage)
            ->appends(request()->query());

        return $content;
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function resolvePaginationParams(): array
    {
        $request = request();

        $this->applyCompatQueryParams($request);

        $currentPage = max(1, (int) (
            $request->query("currentPage")
            ?? $request->query("current_page")
            ?? $request->query("page", 1)
        ));
        $perPage = max(1, min(100, (int) (
            $request->query("limitPage")
            ?? $request->query("limit")
            ?? $request->query("per_page", 10)
        )));

        return [ $currentPage, $perPage, ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function applyCompatQueryParams(\Illuminate\Http\Request $request): void
    {
        if ($request->query("sort") === null && filled($request->query("orders"))) {
            $sorts = collect(QueryParser::parseOrders((string) $request->query("orders")))
                ->map(fn (array $order): string => ($order["direction"] === "desc" ? "-" : "").$order["field"])
                ->implode(",");

            if ($sorts !== "") {
                $request->merge([ "sort" => $sorts, ]);
            }
        }

        if (! $request->has("filter") && filled($request->query("filters"))) {
            $filters = [];

            foreach (QueryParser::parseFilters((string) $request->query("filters")) as $filter) {
                $filters[$filter["field"]] = $filter["search"];
            }

            if ($filters !== []) {
                $request->merge([ "filter" => $filters, ]);
            }
        }
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
