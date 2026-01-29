<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedImportRow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "failed_import_rows";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "data",
        "validation_error",
        "import_id",
    ];

    /**
     * Cast attributes to specific data types.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            "data" => "array",
        ];
    }

    /**
     * Get the import that owns the failed row.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }
}
