<?php

namespace Src\V1\Sample\Models;

use App\Models\User As UserModel;
use Tripteki\Log\Traits\LogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

class SampleModel extends Model
{
    use HasFactory, HasUlids, SoftDeletes, LogTrait;

    /**
     * @var string
     */
    protected $table = "samples";

    /**
     * @var array
     */
    protected $fillable = [ "user_id", "content", ];

    /**
     * @var array
     */
    protected $hidden = [ "user_id", ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Src\V1\Sample\Database\Factories\SampleFactory::new();
    }
};
