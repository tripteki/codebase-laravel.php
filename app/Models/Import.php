<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    /**
     * @var string
     */
    protected $table = "imports";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "completed_at",
        "file_name",
        "file_path",
        "importer",
        "processed_rows",
        "total_rows",
        "successful_rows",
        "user_id",
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "completed_at" => "datetime",
            "processed_rows" => "integer",
            "total_rows" => "integer",
            "successful_rows" => "integer",
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedImportRow::class);
    }

    /**
     * @return int
     */
    public function getFailedRowsCount(): int
    {
        return $this->failedRows()->count();
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
