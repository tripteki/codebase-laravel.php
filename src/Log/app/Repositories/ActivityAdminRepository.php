<?php

namespace Modules\Log\App\Repositories;

use App\Models\Setting;
use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Permission;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;

class ActivityAdminRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => tap(Activity::query()->with("causer.profile"), function ($query): void {
                AdminTenancySupport::applyActiveTenantScope($query);
                $this->applyReaderScope($query);
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

                    $map = array_change_key_case(self::subjectTypeMap(), CASE_LOWER);
                    $mapped = $map[strtolower($term)] ?? $term;
                    $aliases = array_values(array_unique([
                        $mapped,
                        $term,
                        class_basename((string) $mapped),
                    ]));

                    $query->whereIn("subject_type", $aliases);
                }),
                AllowedFilter::exact("event"),
                AllowedFilter::callback("date_from", function ($query, $value): void {
                    $date = self::parseFilterDate($value, false);

                    if ($date !== null) {
                        $query->where("created_at", ">=", $date);
                    }
                }),
                AllowedFilter::callback("date_to", function ($query, $value): void {
                    $date = self::parseFilterDate($value, true);

                    if ($date !== null) {
                        $query->where("created_at", "<=", $date);
                    }
                }),
                AllowedFilter::callback("causer", function ($query, $value): void {
                    $term = trim((string) $value);

                    if ($term === "") {
                        return;
                    }

                    $causerIds = User::query()
                        ->where(function ($nested) use ($term): void {
                            $nested->where("id", $term)
                                ->orWhere("email", "like", "%{$term}%")
                                ->orWhereHas("profile", function ($profile) use ($term): void {
                                    $profile->where("full_name", "like", "%{$term}%");
                                });
                        })
                        ->pluck("id");

                    if ($causerIds->isEmpty()) {
                        $query->whereRaw("1 = 0");

                        return;
                    }

                    $query->whereIn("causer_id", $causerIds);
                }),
                AdminTenancySupport::allowedTenantScope(),
            ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return \Modules\Log\App\Models\Activity
     */
    public function get(string $id): Activity
    {
        return parent::accessGet(
            fn () => tap(Activity::query()->with([ "causer.profile", "subject", ]), function ($query): void {
                $this->applyReaderScope($query);
            })->findOrFail($id),
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
            "Setting" => Setting::class,
        ];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function applyReaderScope(Builder $query): void
    {
        $user = $this->getUser();

        if (! $user instanceof User) {
            return;
        }

        if ($user->hasAnyRole([
            RoleEnum::SUPERADMIN->value,
            RoleEnum::ADMIN->value,
        ])) {
            return;
        }

        $ownerId = (string) $user->getKey();

        $query->where(function ($nested) use ($ownerId): void {
            $nested->where("causer_id", $ownerId)
                ->orWhere(function ($subject) use ($ownerId): void {
                    $subject->where("subject_id", $ownerId)
                        ->whereIn("subject_type", [ User::class, "User", ]);
                });
        });
    }

    /**
     * @param mixed $value
     * @param bool $endOfDay
     * @return \Illuminate\Support\Carbon|null
     */
    private static function parseFilterDate(mixed $value, bool $endOfDay): ?Carbon
    {
        $raw = trim((string) $value);

        if ($raw === "") {
            return null;
        }

        try {
            $date = Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }

        if ($endOfDay && strlen($raw) <= 10) {
            return $date->endOfDay();
        }

        return $date;
    }
}
