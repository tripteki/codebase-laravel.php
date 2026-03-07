<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ContentTrans extends Model
{
    use HasFactory, HasUlids, BelongsToTenant, HasTranslations;

    /**
     * @var string
     */
    protected $table = "content_trans";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "tenant_id",
        "group",
        "key",
        "value",
    ];

    /**
     * @var array<int, string>
     */
    public array $translatable = [
        "value",
    ];
}
