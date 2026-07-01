<?php

namespace Modules\User\App\Dtos;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SettingBatchUpdateDto extends Data
{
    /**
     * @param \Spatie\LaravelData\DataCollection<int, \Modules\User\App\Dtos\SettingRowUpdateDto> $rows
     * @return void
     */
    public function __construct(
        #[DataCollectionOf(SettingRowUpdateDto::class)]
        public DataCollection $rows,
    ) {}

    /**
     * @param \Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            "rows" => [ "sometimes", "array", ],
        ];
    }
}
