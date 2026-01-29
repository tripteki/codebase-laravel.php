<?php

namespace App\Jobs\Base;

use App\Models\Export;
use App\Notifications\ExportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

abstract class ProcessExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Export $export
     * @param string $format
     */
    public function __construct(
        public Export $export,
        public string $format = "csv"
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $data = $this->getExportData();

        $fileName = $this->getFileName() . "." . $this->format;
        $filePath = "exports/" . $fileName;

        $exportClass = $this->getExportClass();
        Excel::store(
            new $exportClass($data),
            $filePath,
            "public",
            $this->getWriterType()
        );

        $this->export->update([
            "file_name" => $fileName,
            "processed_rows" => count($data),
            "successful_rows" => count($data),
            "completed_at" => now(),
        ]);

        $this->export->user->notify(new ExportCompletedNotification($this->export));
    }

    /**
     * Get the export class name.
     *
     * @return string
     */
    abstract protected function getExportClass(): string;

    /**
     * Get export data.
     *
     * @return array
     */
    abstract protected function getExportData(): array;

    /**
     * Get file name for export.
     *
     * @return string
     */
    abstract protected function getFileName(): string;

    /**
     * Get the writer type based on format.
     *
     * @return string
     */
    protected function getWriterType(): string
    {
        return match ($this->format) {
            "xls" => \Maatwebsite\Excel\Excel::XLS,
            "xlsx" => \Maatwebsite\Excel\Excel::XLSX,
            default => \Maatwebsite\Excel\Excel::CSV,
        };
    }
}
