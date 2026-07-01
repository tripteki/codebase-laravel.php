<?php

namespace Modules\Log\App\Dtos;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Log\App\Models\Activity;
use Modules\User\App\Dtos\UserTransformerDto;
use Spatie\LaravelData\Data;

class ActivityTransformerDto extends Data
{
    /**
     * @param string $id
     * @param string|null $log_name
     * @param string|null $tenant_id
     * @param string $description
     * @param string|null $subject_type
     * @param string|null $subject_type_label
     * @param string|null $subject_id
     * @param string|null $event
     * @param string|null $causer_type
     * @param string|null $causer_id
     * @param array<string, mixed>|null $properties
     * @param string|null $batch_uuid
     * @param \DateTimeInterface|null $created_at
     * @param \DateTimeInterface|null $updated_at
     * @param UserTransformerDto|null $causer
     * @return void
     */
    public function __construct(
        public string $id,
        public ?string $log_name,
        public ?string $tenant_id,
        public string $description,
        public ?string $subject_type,
        public ?string $subject_type_label,
        public ?string $subject_id,
        public ?string $event,
        public ?string $causer_type,
        public ?string $causer_id,
        public ?array $properties,
        public ?string $batch_uuid,
        public ?\DateTimeInterface $created_at,
        public ?\DateTimeInterface $updated_at,
        public ?UserTransformerDto $causer = null,
    ) {}

    /**
     * @param Activity $activity
     * @param bool $withDetails
     * @return self
     */
    public static function fromActivity(Activity $activity, bool $withDetails = false): self
    {
        $causer = null;

        if ($activity->relationLoaded("causer") && $activity->causer instanceof User) {
            $causer = UserTransformerDto::fromUser($activity->causer);
        }

        $properties = null;

        if ($withDetails) {
            $rawProperties = $activity->properties;

            if ($rawProperties instanceof Collection) {
                $properties = $rawProperties->toArray();
            } elseif (is_array($rawProperties)) {
                $properties = $rawProperties;
            } elseif ($rawProperties !== null) {
                $properties = (array) $rawProperties;
            }
        }

        return new self(
            id: (string) $activity->getKey(),
            log_name: $activity->log_name,
            tenant_id: $activity->tenant_id !== null ? (string) $activity->tenant_id : null,
            description: (string) $activity->description,
            subject_type: $activity->subject_type,
            subject_type_label: self::subjectTypeLabel($activity->subject_type),
            subject_id: $activity->subject_id !== null ? (string) $activity->subject_id : null,
            event: $activity->event,
            causer_type: $activity->causer_type,
            causer_id: $activity->causer_id !== null ? (string) $activity->causer_id : null,
            properties: $properties,
            batch_uuid: $activity->batch_uuid,
            created_at: self::castDate($activity->created_at),
            updated_at: self::castDate($activity->updated_at),
            causer: $causer,
        );
    }

    /**
     * @param string|null $subjectType
     * @return string|null
     */
    private static function subjectTypeLabel(?string $subjectType): ?string
    {
        if ($subjectType === null || $subjectType === "") {
            return null;
        }

        return class_basename($subjectType);
    }

    /**
     * @param mixed $value
     * @return \DateTimeInterface|null
     */
    private static function castDate(mixed $value): ?\DateTimeInterface
    {
        if ($value === null || $value === "") {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }
}
