<?php

namespace Modules\Log\App\Repositories;

use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminTenancySupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;

class ActivityAdminRepository extends BaseRepository
{
    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => tap(Activity::query()->with("causer"), static function ($query): void {
                AdminTenancySupport::applyActiveTenantScope($query);
            }),
            sortables: [ "id", "log_name", "description", "subject_type", "event", "created_at", ],
            defaultSorts: [ "-created_at", ],
            filterables: [
                AllowedFilter::callback("q", function ($query, $value): void {
                    $term = trim((string) $value);

                    if ($term === "") {
                        return;
                    }

                    $query->where(function ($nested) use ($term): void {
                        $nested->where("description", "like", "%{$term}%")
                            ->orWhere("log_name", "like", "%{$term}%")
                            ->orWhere("event", "like", "%{$term}%");
                    });
                }),
                AllowedFilter::partial("log_name"),
                AllowedFilter::callback("subject_type", function ($query, $value): void {
                    $term = trim((string) $value);

                    if ($term === "") {
                        return;
                    }

                    $mapped = self::subjectTypeMap()[$term] ?? $term;

                    $query->where("subject_type", $mapped);
                }),
                AllowedFilter::exact("event"),
                AdminTenancySupport::allowedTenantScope(),
            ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return Activity
     */
    public function get(string $id): Activity
    {
        return parent::accessGet(
            fn () => Activity::query()->with([ "causer", "subject", ])->findOrFail($id),
        );
    }

    /**
     * @return array<string, string>
     */
    public static function subjectTypeMap(): array
    {
        return [
            "User" => User::class,
            "Role" => Role::class,
            "Permission" => Permission::class,
        ];
    }
}
