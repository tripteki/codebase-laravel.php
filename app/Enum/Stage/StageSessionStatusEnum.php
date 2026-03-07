<?php

namespace App\Enum\Stage;

use Carbon\Carbon;

/**
 * @enum StageSessionStatusEnum
 */
enum StageSessionStatusEnum: string
{
    /**
     * @var string
     */
    case JOIN = "join";

    /**
     * @var string
     */
    case LEAVE = "leave";

    /**
     * @var string
     */
    public const STAGED_JOINED_AT = "joined_at";

    /**
     * @var string
     */
    public const STAGED_LEAVED_AT = "leaved_at";

    /**
     * @var string
     */
    public const STAGED_STATUS = "status";

    /**
     * @return array{joined_at: string, leaved_at: null, status: string}
     */
    public static function defaultStaged(): array
    {
        return [
            self::STAGED_JOINED_AT => Carbon::now()->toIso8601String(),
            self::STAGED_LEAVED_AT => null,
            self::STAGED_STATUS => self::JOIN->value,
        ];
    }
}
