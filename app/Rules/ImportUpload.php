<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImportUpload implements ValidationRule
{
    /**
     * @var int
     */
    public const MAX_BYTES = 5 * 1024 * 1024;

    /**
     * @var array<int, string>
     */
    protected const EXTENSIONS = [ "csv", "txt", "xls", "xlsx", ];

    /**
     * @param string $attribute
     * @param mixed $value
     * @param \Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()
            || ! in_array(strtolower($value->getClientOriginalExtension()), self::EXTENSIONS, true)) {
            $fail(__("upload.import_type"));

            return;
        }

        if ($value->getSize() > self::MAX_BYTES) {
            $fail(__("upload.too_large", [ "size" => self::MAX_BYTES / (1024 * 1024), ]));
        }
    }
}
