<?php

namespace App\Models;

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
use Spatie\Permission\Traits\HasRoles as ACLTrait;
use Spatie\Activitylog\Traits\LogsActivity as LogTrait;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements IAuthSession, IAuthJWT, AuthenticatableContract, AuthorizableContract, VerifyableContract, ResetableContract
{
    use AuthorizableTrait,
        MustVerifyEmailTrait,
        CanResetPasswordTrait,
        NotifiableTrait,
        ACLTrait,
        LogTrait,
        HasUlids,
        SoftDeletes,
        HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        "name",
        "email",
        "password",
        "log_activities",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [

        "password",
        "remember_token",
    ];

    /**
     * Cast attributes to specific data types.
     *
     * @return array
     */
    protected function casts()
    {
        return [

            "email_verified_at" => "datetime",
        ];
    }

    /**
     * Check if the user has access to a given Filament panel.
     *
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    /**
     * Hash the password attribute.
     *
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
     * Set the log_activities attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setLogActivitiesAttribute($value): void
    {
        $this->attributes['log_activities'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the log_activities attribute.
     *
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
     * Get the options for logging activity.
     *
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
     * The identifier that will be used to identify the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * The custom claims for the JWT token.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [

            //
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * Scope a query to only include activated users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeActivated(Builder $query): void
    {
        $query->whereNotNull("email_verified_at");
    }

    /**
     * Scope a query to only include deactivated users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeDeactivated(Builder $query): void
    {
        $query->onlyTrashed();
    }
}
