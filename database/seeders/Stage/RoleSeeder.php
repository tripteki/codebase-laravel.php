<?php

namespace Database\Seeders\Stage;

use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Enum\Stage\StageSessionPermissionEnum;
use Illuminate\Database\Seeder;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        $guard = GuardEnum::WEB->value;

        $admin = Role::where("name", RoleEnum::ADMIN->value)->where("guard_name", $guard)->first();
        $admin?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_CREATE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,
            StageMeetingPermissionEnum::STAGE_MEETING_UPDATE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_DELETE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_IMPORT->value,
            StageMeetingPermissionEnum::STAGE_MEETING_EXPORT->value,

            StageSessionPermissionEnum::STAGE_SESSION_CREATE->value,
            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
            StageSessionPermissionEnum::STAGE_SESSION_UPDATE->value,
            StageSessionPermissionEnum::STAGE_SESSION_DELETE->value,
            StageSessionPermissionEnum::STAGE_SESSION_IMPORT->value,
            StageSessionPermissionEnum::STAGE_SESSION_EXPORT->value,
        ]);

        $delegate = Role::where("name", RoleEnum::DELEGATE->value)->where("guard_name", $guard)->first();
        $delegate?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_CREATE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,
            StageMeetingPermissionEnum::STAGE_MEETING_UPDATE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_DELETE->value,
            StageMeetingPermissionEnum::STAGE_MEETING_IMPORT->value,
            StageMeetingPermissionEnum::STAGE_MEETING_EXPORT->value,

            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
        ]);

        $exhibitor = Role::where("name", RoleEnum::EXHIBITOR->value)->where("guard_name", $guard)->first();
        $exhibitor?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,

            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
        ]);

        $sponsor = Role::where("name", RoleEnum::SPONSOR->value)->where("guard_name", $guard)->first();
        $sponsor?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,

            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
        ]);

        $speaker = Role::where("name", RoleEnum::SPEAKER->value)->where("guard_name", $guard)->first();
        $speaker?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,

            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
        ]);

        $visitor = Role::where("name", RoleEnum::VISITOR->value)->where("guard_name", $guard)->first();
        $visitor?->givePermissionTo([
            StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value,

            StageSessionPermissionEnum::STAGE_SESSION_VIEW->value,
        ]);
    }
}
