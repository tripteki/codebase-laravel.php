<?php

namespace App\Livewire\Admin\User;

use App\Enum\Event\AddOnEnum;
use App\Imports\Admin\User\UserImport;
use App\Helpers\AddOnsHelper;
use App\Jobs\Admin\User\ProcessUserImport;
use App\Livewire\Base\ImportComponent;
use Src\V1\Api\User\Enums\PermissionEnum;

class UserImportComponent extends ImportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::USER_IMPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::FEATURES_IMPORT)) {
            abort(403);
        }
    }
    /**
     * @return string
     */
    protected function getImporterClass(): string
    {
        return UserImport::class;
    }

    /**
     * @return string
     */
    protected function getProcessImportJobClass(): string
    {
        return ProcessUserImport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.user.user-import-component";
    }

    /**
     * @return string
     */
    protected function getImportStartedMessage(): string
    {
        return __("module_base.import_started");
    }

    /**
     * @return string
     */
    protected function getImportUploadPermission(): string
    {
        return PermissionEnum::USER_IMPORT->value;
    }
}
