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
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "exportFormat" => "required|in:csv,xls,xlsx",
        ];
    }

    /**
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
    }

    /**
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
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
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view($this->getViewName());
    }

    /**
     * @return string
     */
    abstract protected function getExporterClass(): string;

    /**
     * @return string
     */
    abstract protected function getProcessExportJobClass(): string;

    /**
     * @return string
     */
    abstract protected function getViewName(): string;

    /**
     * @return int
     */
    abstract protected function getTotalRowsCount(): int;

    /**
     * @return string
     */
    abstract protected function getExportStartedMessage(): string;

    /**
     * @return string
     */
    abstract protected function getExportDownloadPermission(): string;
}
