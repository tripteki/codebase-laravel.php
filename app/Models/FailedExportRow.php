<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedExportRow extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "failed_export_rows";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "data",
        "validation_error",
        "export_id",
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
     * Get the export that owns the failed row.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function export(): BelongsTo
    {
        return $this->belongsTo(Export::class);
    }
}
