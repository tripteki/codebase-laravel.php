<?php

namespace App\Models;

use App\Notifications\VerifyNotification;
use Src\V1\Api\User\Enums\UserEnum;
use Src\V1\Api\User\Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser as IAuthSession;
use Tymon\JWTAuth\Contracts\JWTSubject as IAuthJWT;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as VerifyableContract;
use Illuminate\Contracts\Auth\CanResetPassword as ResetableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable as NotifiableTrait;
use Carbon\Carbon;
use NotificationChannels\WebPush\HasPushSubscriptions as NotifiablePushTrait;
use Spatie\Permission\Traits\HasRoles as ACLTrait;
use Spatie\Activitylog\Traits\LogsActivity as LogTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable implements IAuthSession, IAuthJWT, AuthenticatableContract, AuthorizableContract, VerifyableContract, ResetableContract
{
    use AuthorizableTrait,
        MustVerifyEmailTrait,
        CanResetPasswordTrait,
        NotifiableTrait,
        NotifiablePushTrait,
        ACLTrait,
        LogTrait,
        HasUlids,
        SoftDeletes,
        HasFactory,
        BelongsToTenant;

    /**
     * @var string
     */
    protected $table = "users";

    /**
     * @var array<int, string>
     */
    protected $fillable = [

        "name",
        "email",
        "password",
        "log_activities",
        "tenant_id",
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [

        "password",
        "remember_token",
    ];

    /**
     * @return array<string, string>
     */
    protected function casts()
    {
        return [

            "email_verified_at" => "datetime",
        ];
    }

    /**
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setPasswordAttribute($value): void
    {
        if (empty($value)) {
            return;
        }

        $isAlreadyHashed = is_string($value) &&
                          strlen($value) === 60 &&
                          str_starts_with($value, '$2y$');

        $this->attributes['password'] = $isAlreadyHashed ? $value : Hash::make($value);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setLogActivitiesAttribute($value): void
    {
        $this->attributes['log_activities'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    public function getLogActivitiesAttribute($value): ?array
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        $options = LogOptions::defaults()
            ->logOnly(["name", "email"])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();

        if ($this->log_activities && is_array($this->log_activities) && !empty($this->log_activities)) {
            $options->logEvents($this->log_activities);
        } else {
            $options->dontLogIfAttributesChangedOnly([]);
        }

        return $options;
    }

    /**
     * @param \Spatie\Activitylog\Contracts\Activity $activity
     * @return void
     */
    public function tapActivity(Activity $activity): void
    {
        $activity->tenant_id = $this->tenant_id;
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [

            //
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeActivated(Builder $query): void
    {
        $query->whereNotNull("email_verified_at");
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeDeactivated(Builder $query): void
    {
        $query->onlyTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * @param string|null $date
     * @param string|null $time
     * @return string|null
     */
    public function formatEventDateTime(?string $date, ?string $time): ?string
    {
        if (! filled($date)) {
            return null;
        }
        try {
            $dt = Carbon::parse($date);
            if (filled($time)) {
                $dt->setTimeFromTimeString($time);
                return $dt->format("j M Y, H:i");
            }
            return $dt->format("j M Y");
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyNotification());
    }
}
