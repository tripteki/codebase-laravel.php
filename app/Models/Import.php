<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Import extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "imports";

    /**
     * The attributes that are mass assignable.
     *
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
     * Get the user that owns the import.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the failed import rows.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function failedRows(): HasMany
    {
        return $this->hasMany(FailedImportRow::class);
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
     * Check if the import is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
