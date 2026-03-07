<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Attachable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * @param string $attachment
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(string $attachment): StreamedResponse
    {
        $type = request()->route()->parameter("attachment_type") ?? request()->route()->getDefault("attachment_type");

        if (! $type) {
            abort(404);
        }

        $modelClass = config("attachments.types.{$type}");

        if (! $modelClass || ! is_subclass_of($modelClass, Attachable::class)) {
            abort(404);
        }

        $model = $modelClass::query()->findOrFail($attachment);

        if ($model->getDownloadOwner() === null) {
            abort(404);
        }

        $disk = $model->getStorageDisk();
        $path = $model->getStoragePath();
        $existsOnDisk = Storage::disk($disk)->exists($path);

        if (! $existsOnDisk && $disk === "public") {
            $centralPath = storage_path("app/public/" . $path);
            if (is_file($centralPath)) {
                return response()->download(
                    $centralPath,
                    $model->getDownloadFilename(),
                    ["Content-Type" => $model->getMimeType() ?: "application/octet-stream"]
                );
            }
        }

        if (! $existsOnDisk) {
            abort(404);
        }

        return Storage::disk($disk)->download(
            $path,
            $model->getDownloadFilename(),
            ["Content-Type" => $model->getMimeType() ?: "application/octet-stream"]
        );
    }
}
