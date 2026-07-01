<?php

namespace Modules\User\App\Dtos;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SettingRowUpdateDto extends Data
{
    /**
     * @param string|null $id
     * @param string|null $key
     * @param string|null $value
     * @param string $value_kind
     * @param UploadedFile|null $file
     * @return void
     */
    public function __construct(
        public ?string $id = null,
        public ?string $key = null,
        public ?string $value = null,
        public string $value_kind = "text",
        public ?UploadedFile $file = null,
    ) {}

    /**
     * @param ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            "id" => [ "nullable", "string", ],
            "key" => [ "nullable", "string", "max:255", ],
            "value" => [ "nullable", "string", ],
            "value_kind" => [ "nullable", "string", "in:text,file", ],
            "file" => [ "nullable", "file", "max:10240", ],
        ];
    }
}
