<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class StageMeeting extends Model
{
    use BelongsToTenant, HasFactory, HasUlids, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "stage_meetings";

    /**
     * @var array<string, string>
     */
    protected $casts = [
        "start_at" => "datetime",
        "end_at" => "datetime",
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "tenant_id",
        "room_id",
        "title",
        "description",
        "start_at",
        "end_at",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, "attachable");
    }

    /**
     * @return void
     */
    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->getKey()) {
                $id = (string) Str::ulid();

                $model->setAttribute($model->getKeyName(), $id);
            } else {
                $id = (string) $model->getKey();
            }

            if (! $model->room_id) {
                $model->room_id = substr($id, 0, 6);
            }
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function invitation()
    {
        return $this->morphOne(StageInvitation::class, "invitationable")
            ->where("role", StageInvitation::ROLE_DELEGATE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function invitations()
    {
        return $this->morphMany(StageInvitation::class, "invitationable");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function exhibitorSponsors()
    {
        return $this->hasManyThrough(
            User::class,
            StageInvitation::class,
            "invitationable_id",
            "id",
            "id",
            "user_id"
        )
            ->where("stage_invitations.invitationable_type", self::class)
            ->where("stage_invitations.role", StageInvitation::ROLE_EXHIBITOR_SPONSOR);
    }
}
