<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Setting extends Model
{
    use HasFactory, HasUlids, SoftDeletes, BelongsToTenant;

    /**
     * @var string
     */
    protected $table = "settings";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "key",
        "value",
        "tenant_id",
    ];
}
