<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedExportRow extends Model
{
    /**
     * @var string
     */
    protected $table = "failed_export_rows";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "data",
        "validation_error",
        "export_id",
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "data" => "array",
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function export(): BelongsTo
    {
        return $this->belongsTo(Export::class);
    }
}
