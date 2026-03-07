<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailedImportRow extends Model
{
    /**
     * @var string
     */
    protected $table = "failed_import_rows";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "data",
        "validation_error",
        "import_id",
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
    public function import(): BelongsTo
    {
        return $this->belongsTo(Import::class);
    }
}
