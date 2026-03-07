<?php

namespace App\Enum\Stage;

use Carbon\Carbon;

/**
 * @enum StageMeetingStatusEnum
 */
enum StageMeetingStatusEnum: string
{
    /**
     * @var string
     */
    case QUEUE = "queue";

    /**
     * @var string
     */
    case VIEW = "view";

    /**
     * @var string
     */
    public const STAGED_QUEUED_AT = "queued_at";

    /**
     * @var string
     */
    public const STAGED_VIEWED_AT = "viewed_at";

    /**
     * @var string
     */
    public const STAGED_STATUS = "status";

    /**
     * @return array{queued_at: string, viewed_at: null, status: string}
     */
    public static function defaultStaged(): array
    {
        return [
            self::STAGED_QUEUED_AT => Carbon::now()->toIso8601String(),
            self::STAGED_VIEWED_AT => null,
            self::STAGED_STATUS => self::QUEUE->value,
        ];
    }
}
