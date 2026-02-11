<?php

namespace App\Jobs\Base;

use App\Models\FailedImportRow;
use App\Models\Import;
use App\Notifications\ImportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

abstract class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Import $import
     */
    public function __construct(
        public Import $import
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $filePath = Storage::disk("public")->path($this->import->file_path);

        if (! file_exists($filePath)) {
            return;
        }

        $importClass = $this->getImportClass();
        $import = new $importClass();
        $rows = Excel::toArray($import, $filePath);
        $data = $rows[0] ?? [];
        
        if (empty($data)) {
            $this->import->update([
                "completed_at" => now(),
            ]);
            return;
        }

        $this->import->update([
            "total_rows" => count($data),
        ]);

        $successfulRows = 0;
        $processedRows = 0;
        $failedRowsCount = 0;

        foreach ($data as $row) {
            $processedRows++;

            $rowData = is_array($row) ? $row : [];
            $rowData = array_map(function($value) {
                return is_string($value) ? trim($value) : $value;
            }, $rowData);

            $normalizedData = $this->normalizeRowData($rowData);

            $validator = $this->getValidator($normalizedData);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                $errorMessage = implode("; ", $errors);

                $this->saveFailedRow($normalizedData, $errorMessage);
                $failedRowsCount++;

                $this->import->update([
                    "processed_rows" => $processedRows,
                    "successful_rows" => $successfulRows,
                ]);

                continue;
            }

            $validatedData = $validator->validated();

            try {
                $this->validateBeforeProcess($validatedData, $normalizedData);
            } catch (\Exception $e) {
                $this->saveFailedRow($normalizedData, $e->getMessage());
                $failedRowsCount++;

                $this->import->update([
                    "processed_rows" => $processedRows,
                    "successful_rows" => $successfulRows,
                ]);

                continue;
            }

            DB::beginTransaction();

            try {
                $this->processRow($validatedData, $normalizedData);

                DB::commit();

                $successfulRows++;
            } catch (\Exception $e) {
                DB::rollBack();

                $this->saveFailedRow($normalizedData, $e->getMessage());
                $failedRowsCount++;
            }

            $this->import->update([
                "processed_rows" => $processedRows,
                "successful_rows" => $successfulRows,
            ]);
        }

        $this->import->refresh();

        $this->import->update([
            "completed_at" => now(),
        ]);

        $this->import->refresh();

        $this->import->user->notify(new ImportCompletedNotification($this->import));
    }

    /**
     * Get the import class name.
     *
     * @return string
     */
    abstract protected function getImportClass(): string;

    /**
     * Normalize row data.
     *
     * @param array $rowData
     * @return array
     */
    protected function normalizeRowData(array $rowData): array
    {
        $normalizedData = [];
        foreach ($rowData as $key => $value) {
            $normalizedKey = strtolower(trim($key));
            $normalizedData[$normalizedKey] = $value !== null ? (string) $value : null;
        }

        return $normalizedData;
    }

    /**
     * Get validator for row data.
     *
     * @param array $normalizedData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    abstract protected function getValidator(array $normalizedData): \Illuminate\Contracts\Validation\Validator;

    /**
     * Validate before processing (optional, for additional business logic validation).
     *
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     * @throws \Exception
     */
    protected function validateBeforeProcess(array $validatedData, array $normalizedData): void
    {
        //
    }

    /**
     * Process a single row.
     *
     * @param array $validatedData
     * @param array $normalizedData
     * @return void
     */
    abstract protected function processRow(array $validatedData, array $normalizedData): void;

    /**
     * Save failed import row.
     *
     * @param array $normalizedData
     * @param string $errorMessage
     * @return void
     */
    protected function saveFailedRow(array $normalizedData, string $errorMessage): void
    {
        try {
            $dataToStore = array_filter($normalizedData, function($value) {
                return $value !== null && $value !== "";
            });

            FailedImportRow::query()->create([
                "import_id" => $this->import->id,
                "data" => $dataToStore,
                "validation_error" => $errorMessage,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save failed import row: " . $e->getMessage(), [
                "import_id" => $this->import->id,
                "data" => $normalizedData,
                "exception" => $e->getTraceAsString(),
            ]);
        }
    }
}
