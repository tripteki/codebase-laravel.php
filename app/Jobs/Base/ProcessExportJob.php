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
     * @param \App\Models\Export $export
     * @param string $format
     * @return void
     */
    public function __construct(
        public Export $export,
        public string $format = "csv"
    ) {
    }

    /**
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
     * @return string
     */
    abstract protected function getExportClass(): string;

    /**
     * @return array
     */
    abstract protected function getExportData(): array;

    /**
     * @return string
     */
    abstract protected function getFileName(): string;

    /**
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
