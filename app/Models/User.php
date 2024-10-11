<?php

namespace App\Models;

use Tripteki\Log\Traits\LogCauseTrait;
use Tripteki\SettingProfile\Traits\ProfileTrait;
use Tripteki\Setting\Traits\SettingTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Authenticatable implements AuthenticatableContract, AuthorizableContract
{
    use Authorizable, HasFactory, SettingTrait, ProfileTrait, LogCauseTrait;

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
}
