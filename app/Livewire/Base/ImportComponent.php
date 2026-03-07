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
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "importFile" => "required|file|mimes:csv,xls,xlsx|max:10240",
        ];
    }

    /**
     * @return void
     */
    public function openModal(): void
    {
        $this->showModal = true;
        $this->reset("importFile");
    }

    /**
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset("importFile");
    }

    /**
     * @return void
     */
    public function import(): void
    {
        $this->authorize($this->getImportUploadPermission());

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
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view($this->getViewName());
    }

    /**
     * @return string
     */
    abstract protected function getImporterClass(): string;

    /**
     * @return string
     */
    abstract protected function getProcessImportJobClass(): string;

    /**
     * @return string
     */
    abstract protected function getViewName(): string;

    /**
     * @return string
     */
    abstract protected function getImportStartedMessage(): string;

    /**
     * @return string
     */
    abstract protected function getImportUploadPermission(): string;
}
