<?php

namespace App\Livewire\Base;

use App\Models\Export;
use Illuminate\View\View;
use Livewire\Component;

abstract class ExportComponent extends Component
{
    /**
     * @var string
     */
    public $exportFormat = "csv";

    /**
     * @var bool
     */
    public $showModal = false;

    /**
     * Validation rules for export format.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "exportFormat" => "required|in:csv,xls,xlsx",
        ];
    }

    /**
     * Open export modal.
     *
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     * Close export modal.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * Handle export request.
     *
     * @return void
     */
    public function export(): void
    {
        $this->authorize($this->getExportDownloadPermission());

        $this->validate([
            "exportFormat" => "required|in:csv,xls,xlsx",
        ]);

        $export = Export::query()->create([
            "file_disk" => "public",
            "exporter" => $this->getExporterClass(),
            "total_rows" => $this->getTotalRowsCount(),
            "user_id" => auth()->id(),
        ]);

        $this->getProcessExportJobClass()::dispatch($export, $this->exportFormat);

        $this->closeModal();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view($this->getViewName());
    }

    /**
     * Get the exporter class name.
     *
     * @return string
     */
    abstract protected function getExporterClass(): string;

    /**
     * Get the process export job class name.
     *
     * @return string
     */
    abstract protected function getProcessExportJobClass(): string;

    /**
     * Get the view name.
     *
     * @return string
     */
    abstract protected function getViewName(): string;

    /**
     * Get total rows count for export.
     *
     * @return int
     */
    abstract protected function getTotalRowsCount(): int;

    /**
     * Get export started message.
     *
     * @return string
     */
    abstract protected function getExportStartedMessage(): string;

    /**
     * Get export download permission.
     *
     * @return string
     */
    abstract protected function getExportDownloadPermission(): string;
}
