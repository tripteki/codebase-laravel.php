<?php

namespace App\Livewire\Base;

use App\Models\Import;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

abstract class ImportComponent extends Component
{
    use WithFileUploads;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $importFile = null;

    /**
     * @var bool
     */
    public $showModal = false;

    /**
     * Validation rules for import file.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "importFile" => "required|file|mimes:csv,xls,xlsx|max:10240",
        ];
    }

    /**
     * Open import modal.
     *
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset("importFile");
    }

    /**
     * Close import modal.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset("importFile");
    }

    /**
     * Handle import file upload and process.
     *
     * @return void
     */
    public function import(): void
    {
        $this->validate([
            "importFile" => "required|file|mimes:csv,xls,xlsx|max:10240",
        ]);

        $file = $this->importFile;
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store("imports", "public");

        $import = Import::query()->create([
            "file_name" => $fileName,
            "file_path" => $filePath,
            "importer" => $this->getImporterClass(),
            "total_rows" => 0,
            "user_id" => auth()->id(),
        ]);

        $this->getProcessImportJobClass()::dispatch($import);

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
     * Get the importer class name.
     *
     * @return string
     */
    abstract protected function getImporterClass(): string;

    /**
     * Get the process import job class name.
     *
     * @return string
     */
    abstract protected function getProcessImportJobClass(): string;

    /**
     * Get the view name.
     *
     * @return string
     */
    abstract protected function getViewName(): string;

    /**
     * Get import started message.
     *
     * @return string
     */
    abstract protected function getImportStartedMessage(): string;
}
