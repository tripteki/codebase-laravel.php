<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Attachable
{
    /**
     * @return string
     */
    public function getStorageDisk(): string;

    /**
     * @return string
     */
    public function getStoragePath(): string;

    /**
     * @return string
     */
    public function getDownloadFilename(): string;

    /**
     * @return string|null
     */
    public function getMimeType(): ?string;

    /**
     * @return Model|null
     */
    public function getDownloadOwner(): ?Model;
}
