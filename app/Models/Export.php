<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Export extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "exports";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "completed_at",
        "file_name",
        "file_disk",
        "exporter",
        "processed_rows",
        "total_rows",
        "successful_rows",
        "user_id",
    ];

    /**
     * Cast attributes to specific data types.
     *
     * @return array
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
     * Get the user that owns the export.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the failed export rows.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedExportRow::class);
    }

    /**
     * Get the count of failed rows.
     *
     * @return int
     */
    public function getFailedRowsCount(): int
    {
        return $this->failedRows()->count();
    }

    /**
     * Check if the export is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
