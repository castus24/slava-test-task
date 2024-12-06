<?php

namespace App\Jobs;

use App\Imports\RowsImport;
use App\Models\Row;
use App\Services\ValidatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected string $redisKey;
    private ValidatorService $validator;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->redisKey = 'import_' . uniqid();
        $this->validator = app(ValidatorService::class);
    }

    /**
     * @throws Throwable
     */
    public function handle(RowsImport $import): void
    {
        $data = Excel::toArray($import, storage_path('app/' . $this->filePath));
        $rows = array_slice($data[0] ?? [], 1);
        $chunkSize = 1000;
        $errors = [];
        $processedCount = 0;
        $totalRows = count($rows);

        $this->updateRedisProgress($processedCount, $totalRows);

        collect($rows)
            ->chunk($chunkSize)
            ->each(function ($chunk) use (&$errors, &$processedCount, $totalRows) {
                $this->processChunk($chunk, $errors, $processedCount, $totalRows);
            });

        file_put_contents(base_path('result.txt'), implode("\n", $errors));

        $this->updateRedisProgress($processedCount, $totalRows, true);
    }

    /**
     * @throws Throwable
     */
    private function processChunk(Collection $chunk, array &$errors, int &$processedCount, int $totalRows): void
    {
        $validRows = [];
        $lineNumber = $processedCount;

        $chunk->each(function ($row) use (&$validRows, &$errors, &$lineNumber) {
            $lineNumber++;
            $validationResult = $this->validator->validateRow($row, $lineNumber);

            if ($validationResult['is_valid']) {
                $validRows[] = $validationResult['data'];
            } else {
                $errors[] = $validationResult['error'];
            }
        });

        if (!empty($validRows)) {
            try {
                Row::query()->upsert($validRows, ['developer_id'], ['name', 'date', 'updated_at']);
            } catch (Throwable $e) {
                Log::error('Upsert Error', ['exception' => $e->getMessage(), 'validRows' => $validRows]);
                throw $e;
            }
        }

        $processedCount += $chunk->count();

        $this->updateRedisProgress($processedCount, $totalRows);
    }

    private function updateRedisProgress(int $processed, int $total, bool $isComplete = false): void
    {
        $progressData = json_encode([
            'processed' => $processed,
            'total' => $total,
            'status' => $isComplete ? 'complete' : 'in_progress',
        ]);

        Redis::command('SET', [$this->redisKey, $progressData]);
    }
}
