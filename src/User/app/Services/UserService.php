<?php

namespace Modules\User\App\Services;

use App\Models\User;
use App\Services\Service as BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\User\App\Dtos\UserAccessTransformerDto;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserMeTransformerDto;
use Modules\User\App\Dtos\UserMeUpdateDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Repositories\UserRepository;
use Modules\User\App\Support\EmailChange;
use Modules\User\App\Support\SecurityNotifier;

class UserService extends BaseService
{
    /**
     * @var \Modules\User\App\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * @param \Modules\User\App\Repositories\UserRepository $userRepository
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function get(): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @return \Modules\User\App\Dtos\UserMeTransformerDto
     */
    public function getMe(): UserMeTransformerDto
    {
        return UserMeTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @param \Modules\User\App\Dtos\UserMeUpdateDto $userData
     * @param \Illuminate\Http\UploadedFile|null $avatar
     * @return \Modules\User\App\Dtos\UserMeTransformerDto
     */
    public function updateMe(UserMeUpdateDto $userData, ?UploadedFile $avatar = null): UserMeTransformerDto
    {
        $user = $this->userRepository->get();
        $avatarPath = null;
        $payload = $userData->userPayload();
        $nextEmail = trim((string) ($payload["email"] ?? $user->email));
        $emailChanged = strcasecmp($nextEmail, (string) $user->email) !== 0;

        AuthAddOnsHelper::initializeForUser($user);

        if ($emailChanged) {
            User::ensureEmailIsAvailable(
                $nextEmail,
                (string) $user->getKey(),
                $user->tenant_id !== null ? (string) $user->tenant_id : null,
            );
        }

        $nextName = trim((string) ($payload["name"] ?? ""));

        if ($nextName !== "" && $nextName !== (string) $user->name) {
            User::ensureNameIsAvailable(
                $nextName,
                (string) $user->getKey(),
                $user->tenant_id !== null ? (string) $user->tenant_id : null,
            );
        }

        if ($emailChanged && AuthAddOnsHelper::isMailingEnabled()) {
            unset($payload["email"]);
            EmailChange::request($user, $nextEmail);
        }

        if ($avatar instanceof UploadedFile) {
            if ($user->profile?->avatar) {
                Storage::disk("public")->delete($user->profile->avatar);
            }

            $avatarPath = $avatar->store("avatars", "public");
        }

        $updated = $this->userRepository->updateMe(
            $user,
            $payload,
            $userData->profilePayload(),
            $avatarPath,
        );

        if (filled($userData->password)) {
            SecurityNotifier::passwordChanged($updated, (string) $user->getKey());
        }

        return UserMeTransformerDto::fromUser($updated);
    }

    /**
     * @return array<int, string>
     */
    public function profileInterests(): array
    {
        return $this->userRepository->profileInterests();
    }

    /**
     * @return \Modules\User\App\Dtos\UserAccessTransformerDto
     */
    public function access(): UserAccessTransformerDto
    {
        return UserAccessTransformerDto::fromUser($this->userRepository->get());
    }

    /**
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @param string|null $tenantId
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function create(UserDto $userData, ?string $tenantId = null): UserTransformerDto
    {
        $userPayload = $userData->createPayload();

        if (filled($tenantId)) {
            $userPayload["tenant_id"] = $tenantId;
        }

        User::ensureNameIsAvailable((string) ($userPayload["name"] ?? ""), null, $tenantId);
        User::ensureEmailIsAvailable((string) ($userPayload["email"] ?? ""), null, $tenantId);

        return UserTransformerDto::fromUser($this->userRepository->create(
            $userPayload,
            $userData->profilePayload(),
        ));
    }
}
