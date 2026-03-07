<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentLocale extends Model
{
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = "content_locales";

    /**
     * @var string
     */
    protected $primaryKey = "code";

    /**
     * @var string
     */
    protected $keyType = "string";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "code",
    ];
}
