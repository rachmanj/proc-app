<?php

namespace App\Console\Commands;

use App\Services\SapService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DumpSapPoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sap:dump-po 
        {--start= : Start date (YYYY-MM-DD, default: today UTC+8)} 
        {--end= : End date (YYYY-MM-DD, default: today UTC+8)} 
        {--po= : Comma separated PO numbers to filter} 
        {--limit=0 : Limit the number of rows shown in console output} 
        {--path= : Custom relative path for JSON dump (default: storage/app/sap-po-dump-*.json)} 
        {--no-file : Skip writing JSON dump file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SAP PO SQL query and dump the raw results for debugging';

    /**
     * Execute the console command.
     */
    public function handle(SapService $sapService): int
    {
        try {
            [$startDate, $endDate] = $this->resolveDateRange();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->info("Fetching SAP PO data from {$startDate} to {$endDate} ...");

        try {
            $results = collect($sapService->executePoSqlQuery($startDate, $endDate));
        } catch (\Throwable $e) {
            $this->error('Failed to execute SAP query: ' . $e->getMessage());
            return self::FAILURE;
        }

        $results = $this->filterByDocNum($results);

        $rowCount = $results->count();
        $poCount = $results->pluck('po_no')->filter()->unique()->count();

        $this->line("→ Rows fetched: {$rowCount}");
        $this->line("→ Distinct PO numbers: {$poCount}");

        if ($rowCount === 0) {
            $this->warn('No data returned for the specified filters.');
            return self::SUCCESS;
        }

        $this->renderPreviewTable($results);

        if ($this->option('no-file')) {
            $this->comment('Skipping file write (--no-file specified).');
            return self::SUCCESS;
        }

        $path = $this->option('path') ?: 'sap-po-dump-' . now('Asia/Jakarta')->format('Ymd_His') . '.json';

        try {
            Storage::disk('local')->put($path, $results->values()->map(function ($row) {
                return (array) $row;
            })->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            $this->error('Failed to write JSON dump: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Raw data saved to: storage/app/' . $path);

        return self::SUCCESS;
    }

    /**
     * Determine the date range for the query.
     *
     * @return array{string,string}
     *
     * @throws ValidationException
     */
    protected function resolveDateRange(): array
    {
        $tz = 'Asia/Jakarta';
        $startOption = $this->option('start');
        $endOption = $this->option('end');

        $start = $startOption ? Carbon::parse($startOption, $tz) : now($tz);
        $end = $endOption ? Carbon::parse($endOption, $tz) : $start;

        if ($end->lt($start)) {
            throw ValidationException::withMessages([
                'end' => ['End date must be after or equal to start date.'],
            ]);
        }

        return [$start->copy()->startOfDay()->format('Y-m-d'), $end->copy()->startOfDay()->format('Y-m-d')];
    }

    /**
     * Optionally filter results by comma-separated PO numbers.
     */
    protected function filterByDocNum(Collection $results): Collection
    {
        $poFilter = $this->option('po');

        if (!$poFilter) {
            return $results;
        }

        $poNumbers = collect(explode(',', $poFilter))
            ->map(fn ($po) => trim($po))
            ->filter()
            ->all();

        return $results->filter(function ($row) use ($poNumbers) {
            return in_array($row->po_no ?? null, $poNumbers, true);
        })->values();
    }

    /**
     * Render a table preview in the console.
     */
    protected function renderPreviewTable(Collection $results): void
    {
        $limit = (int) $this->option('limit');
        $rows = $limit > 0 ? $results->take($limit) : $results;

        $tableRows = $rows->map(function ($row) {
            return [
                'po_no' => $row->po_no ?? null,
                'item_code' => $row->item_code ?? null,
                'description' => $row->description ?? null,
                'remark1' => $row->remark1 ?? null,
                'remark2' => $row->remark2 ?? null,
                'qty' => $row->qty ?? null,
                'unit_price' => $row->unit_price ?? null,
                'item_amount' => $row->item_amount ?? null,
            ];
        })->all();

        $this->table(
            ['PO No', 'Item Code', 'Description', 'Remark 1', 'Remark 2', 'Qty', 'Unit Price', 'Item Amount'],
            $tableRows
        );

        if ($limit > 0 && $results->count() > $limit) {
            $this->comment("Showing {$limit} of {$results->count()} rows (use --limit=0 to show all).");
        }
    }
}
