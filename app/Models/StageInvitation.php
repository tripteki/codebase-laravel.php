<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class StageInvitation extends Model
{
    use BelongsToTenant, HasFactory, HasUlids;

    /**
     * @var string
     */
    public const ROLE_DELEGATE = "delegate";

    /**
     * @var string
     */
    public const ROLE_SPEAKER = "speaker";

    /**
     * @var string
     */
    public const ROLE_EXHIBITOR_SPONSOR = "exhibitor_sponsor";

    /**
     * @var string
     */
    protected $table = "stage_invitations";

    /**
     * @var array<string, string>
     */
    protected $casts = [
        "staged" => "array",
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "tenant_id",
        "invitationable_type",
        "invitationable_id",
        "role",
        "user_id",
        "staged",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function invitationable()
    {
        return $this->morphTo("invitationable");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
