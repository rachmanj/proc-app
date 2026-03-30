<?php

namespace App\Console\Commands;

use App\Services\SapSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use InvalidArgumentException;

class SapSyncCommand extends Command
{
    protected $signature = 'sap:sync
        {--pr : Sync purchase requisitions only}
        {--po : Sync purchase orders only}
        {--all : Sync both PR and PO (same as default)}
        {--start-date= : Start date (Y-m-d)}
        {--end-date= : End date (Y-m-d)}
        {--today : Use today as both start and end (app timezone)}
        {--yesterday : Use yesterday as both start and end}
        {--days= : Last N days inclusive ending today; default 7 when no other date option is used}';

    protected $description = 'Sync PR and/or PO from SAP B1 (same logic as Master → Sync With SAP)';

    public function handle(SapSyncService $sapSyncService): int
    {
        try {
            [$startDate, $endDate] = $this->resolveDateRange();
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $runPr = false;
        $runPo = false;
        $hasPr = (bool) $this->option('pr');
        $hasPo = (bool) $this->option('po');
        $hasAll = (bool) $this->option('all');

        if ($hasAll || ($hasPr && $hasPo) || (! $hasPr && ! $hasPo)) {
            $runPr = true;
            $runPo = true;
        } elseif ($hasPr) {
            $runPr = true;
        } else {
            $runPo = true;
        }

        $userId = $this->resolveSyncUserId();

        $this->info("Date range: {$startDate} → {$endDate} (".config('app.timezone').')');
        $this->line('Targets: '.($runPr ? 'PR ' : '').($runPo ? 'PO' : ''));
        if ($userId !== null) {
            $this->line("Sync log user_id: {$userId} (SAP_SYNC_USER_ID)");
        } else {
            $this->line('Sync log user_id: (null — set SAP_SYNC_USER_ID in .env to attribute scheduled runs)');
        }

        $failed = false;

        if ($runPr) {
            $this->newLine();
            $this->info('Running PR sync...');
            $result = $sapSyncService->syncPr($startDate, $endDate, $userId);
            $this->line($result['message']);
            if (! $result['success']) {
                $failed = true;
            }
        }

        if ($runPo) {
            $this->newLine();
            $this->info('Running PO sync...');
            $result = $sapSyncService->syncPo($startDate, $endDate, $userId);
            $this->line($result['message']);
            if (! $result['success']) {
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function resolveDateRange(): array
    {
        $tz = config('app.timezone');
        $today = $this->option('today');
        $yesterday = $this->option('yesterday');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $days = $this->option('days');

        $modeCount = 0;
        if ($today) {
            $modeCount++;
        }
        if ($yesterday) {
            $modeCount++;
        }
        if ($startDate || $endDate) {
            $modeCount++;
        }
        if ($days !== null && $days !== '') {
            $modeCount++;
        }

        if ($modeCount > 1) {
            throw new InvalidArgumentException('Use only one of: --today, --yesterday, --start-date with --end-date, or --days.');
        }

        if ($today) {
            $d = Carbon::now($tz)->startOfDay();

            return [$d->format('Y-m-d'), $d->format('Y-m-d')];
        }

        if ($yesterday) {
            $d = Carbon::now($tz)->subDay()->startOfDay();

            return [$d->format('Y-m-d'), $d->format('Y-m-d')];
        }

        if ($startDate || $endDate) {
            if (! $startDate || ! $endDate) {
                throw new InvalidArgumentException('Both --start-date and --end-date are required together.');
            }
            $start = Carbon::parse($startDate, $tz)->startOfDay();
            $end = Carbon::parse($endDate, $tz)->startOfDay();
            if ($end->lt($start)) {
                throw new InvalidArgumentException('End date must be on or after start date.');
            }

            return [$start->format('Y-m-d'), $end->format('Y-m-d')];
        }

        $n = ($days !== null && $days !== '') ? (int) $days : 7;
        if ($n < 1) {
            throw new InvalidArgumentException('--days must be at least 1.');
        }
        $end = Carbon::now($tz)->startOfDay();
        $start = $end->copy()->subDays($n - 1);

        return [$start->format('Y-m-d'), $end->format('Y-m-d')];
    }

    protected function resolveSyncUserId(): ?int
    {
        $raw = config('services.sap.sync_user_id');
        if ($raw === null || $raw === '') {
            return null;
        }

        return (int) $raw;
    }
}
