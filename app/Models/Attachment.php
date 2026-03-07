<?php

namespace App\Models;

use App\Contracts\Attachable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Attachment extends Model implements Attachable
{
    use BelongsToTenant, HasFactory, HasUlids;

    /**
     * @var string
     */
    protected $table = "attachments";

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        "tenant_id",
        "attachable_type",
        "attachable_id",
        "disk",
        "path",
        "original_name",
        "mime_type",
        "size",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return string
     */
    public function getStorageDisk(): string
    {
        return $this->disk ?? "public";
    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getDownloadFilename(): string
    {
        return $this->original_name;
    }

    /**
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getDownloadOwner(): ?Model
    {
        return $this->attachable;
    }

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, "image/");
    }

    /**
     * @return string
     */
    public function url(): string
    {
        $disk = $this->disk ?? "public";

        if (! $this->path) {
            return "";
        }

        if ($disk === "public") {
            return asset("storage/" . $this->path);
        }

        if (! Storage::disk($disk)->exists($this->path)) {
            return "";
        }

        return Storage::disk($disk)->url($this->path);
    }
}
