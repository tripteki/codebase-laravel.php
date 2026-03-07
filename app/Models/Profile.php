<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, HasUlids;

    /**
     * @var string
     */
    protected $table = "profiles";

    /**
     * @var array<string, string>
     */
    protected $casts = [
        "interests" => "array",
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "full_name",
        "avatar",
        "interests",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
