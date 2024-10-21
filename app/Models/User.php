<?php

namespace App\Models;

use Tripteki\Uid\Traits\UniqueIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tripteki\ACL\Traits\RolePermissionTrait;
use Tripteki\Log\Traits\LogCauseTrait;
use Tripteki\SettingProfile\Traits\ProfileTrait;
use Tripteki\Setting\Traits\SettingTrait;
use Tymon\JWTAuth\Contracts\JWTSubject as IAuthJWT;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as VerifyableContract;
use Illuminate\Contracts\Auth\CanResetPassword as ResetableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable as AuthorizableTrait;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable as NotifiableTrait;

class User extends Authenticatable implements IAuthJWT, AuthenticatableContract, AuthorizableContract, VerifyableContract, ResetableContract
{
    use AuthorizableTrait, MustVerifyEmailTrait, CanResetPasswordTrait, NotifiableTrait, UniqueIdTrait, SoftDeletes, HasFactory, SettingTrait, ProfileTrait, LogCauseTrait, RolePermissionTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        "name",
        "email",
        "password",
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
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [

            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
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
        return (array) null;
    }
}
