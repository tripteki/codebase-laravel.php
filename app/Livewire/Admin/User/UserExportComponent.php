<?php

namespace App\Livewire\Admin\User;

use App\Enum\Event\AddOnEnum;
use App\Exports\Admin\User\UserExport;
use App\Helpers\AddOnsHelper;
use App\Jobs\Admin\User\ProcessUserExport;
use App\Livewire\Base\ExportComponent;
use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;

class UserExportComponent extends ExportComponent
{
    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::USER_EXPORT->value);
        if (! AddOnsHelper::has(AddOnEnum::FEATURES_EXPORT)) {
            abort(403);
        }
    }
    /**
     * @return string
     */
    protected function getExporterClass(): string
    {
        return UserExport::class;
    }

    /**
     * @return string
     */
    protected function getProcessExportJobClass(): string
    {
        return ProcessUserExport::class;
    }

    /**
     * @return string
     */
    protected function getViewName(): string
    {
        return "livewire.admin.user.user-export-component";
    }

    /**
     * @return int
     */
    protected function getTotalRowsCount(): int
    {
        return User::query()->count();
    }

    /**
     * @return string
     */
    protected function getExportStartedMessage(): string
    {
        return __("module_base.export_started");
    }

    /**
     * @return string
     */
    protected function getExportDownloadPermission(): string
    {
        return PermissionEnum::USER_EXPORT->value;
    }
}
