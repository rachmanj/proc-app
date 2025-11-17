<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SapService
{
    /**
     * Execute PO SQL query from SAP B1 SQL Server
     *
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array
     */
    public function executePoSqlQuery($startDate, $endDate)
    {
        try {
            $sqlPath = database_path('list_po.sql');
            
            if (!file_exists($sqlPath)) {
                throw new \Exception("SQL file not found: {$sqlPath}");
            }

            $sql = file_get_contents($sqlPath);
            
            // Format dates for SQL Server (YYYY-MM-DD HH:MM:SS)
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';
            
            // Replace placeholders with actual date values (SQL Server DECLARE requires direct value assignment)
            $sql = str_replace("'[%0]'", "'" . $startDateTime . "'", $sql);
            $sql = str_replace("'[%1]'", "'" . $endDateTime . "'", $sql);
            
            // Remove FOR BROWSE if present
            $sql = str_replace('FOR BROWSE', '', $sql);
            
            Log::info('Executing PO SQL query', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $results = DB::connection('sap_sql')->select($sql);
            
            Log::info('PO SQL query executed successfully', [
                'record_count' => count($results),
            ]);

            return $results;
        } catch (\Exception $e) {
            Log::error('Error executing PO SQL query: ' . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Execute PR SQL query from SAP B1 SQL Server
     *
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array
     */
    public function executePrSqlQuery($startDate, $endDate)
    {
        try {
            $sqlPath = database_path('list_pr_generated.sql');
            
            if (!file_exists($sqlPath)) {
                throw new \Exception("SQL file not found: {$sqlPath}");
            }

            $sql = file_get_contents($sqlPath);
            
            // Format dates for SQL Server (YYYY-MM-DD HH:MM:SS)
            $startDateTime = $startDate . ' 00:00:00';
            $endDateTime = $endDate . ' 23:59:59';
            
            // Replace placeholders with actual date values (SQL Server DECLARE requires direct value assignment)
            $sql = str_replace("'[%0]'", "'" . $startDateTime . "'", $sql);
            $sql = str_replace("'[%1]'", "'" . $endDateTime . "'", $sql);
            
            Log::info('Executing PR SQL query', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $results = DB::connection('sap_sql')->select($sql);
            
            Log::info('PR SQL query executed successfully', [
                'record_count' => count($results),
            ]);

            return $results;
        } catch (\Exception $e) {
            Log::error('Error executing PR SQL query: ' . $e->getMessage(), [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Map PO SQL result to PoTemp model structure
     *
     * @param object $row
     * @return array
     */
    public function mapPoResultToModel($row)
    {
        return [
            'po_no' => $row->po_no ?? null,
            'posting_date' => $this->convertSqlDate($row->posting_date ?? null),
            'create_date' => $this->convertSqlDate($row->create_date ?? null),
            'po_delivery_date' => $this->convertSqlDate($row->po_delivery_date ?? null),
            'po_eta' => $this->convertSqlDate($row->po_eta ?? null),
            'pr_no' => $row->pr_no ?? null,
            'vendor_code' => $row->vendor_code ?? null,
            'vendor_name' => $row->vendor_name ?? null,
            'unit_no' => $row->unit_no ?? null,
            'item_code' => $row->item_code ?? null,
            'description' => $row->description ?? null,
            'remark1' => $row->remark1 ?? null,
            'remark2' => $row->remark2 ?? null,
            'qty' => $row->qty ?? null,
            'po_currency' => $row->po_currency ?? null,
            'unit_price' => $row->unit_price ?? null,
            'item_amount' => $row->item_amount ?? null,
            'total_po_price' => $row->total_po_price ?? null,
            'po_with_vat' => $row->po_with_vat ?? null,
            'uom' => $row->uom ?? null,
            'project_code' => $row->project_code ?? null,
            'dept_code' => $row->dept_code ?? null,
            'po_status' => $row->po_status ?? null,
            'po_delivery_status' => $row->po_delivery_status ?? null,
            'budget_type' => $row->budget_type ?? null,
        ];
    }

    /**
     * Map PR SQL result to PrTemp model structure
     *
     * @param object $row
     * @return array
     */
    public function mapPrResultToModel($row)
    {
        return [
            'pr_draft_no' => $row->pr_draft_no ?? null,
            'pr_no' => $row->pr_no ?? null,
            'pr_date' => $this->convertSqlDate($row->pr_date ?? null),
            'generated_date' => $this->convertSqlDate($row->generated_date ?? null),
            'priority' => $row->priority ?? null,
            'pr_status' => $row->pr_status ?? null,
            'closed_status' => $row->closed_status ?? null,
            'pr_rev_no' => $row->pr_rev_no ?? null,
            'pr_type' => $row->pr_type ?? null,
            'project_code' => $row->project_code ?? null,
            'dept_name' => $row->dept_name ?? null,
            'for_unit' => $row->for_unit ?? null,
            'hours_meter' => $row->hours_meter ?? null,
            'required_date' => $this->convertSqlDate($row->required_date ?? null),
            'requestor' => $row->requestor ?? null,
            'item_code' => $row->item_code ?? null,
            'item_name' => $row->item_name ?? null,
            'quantity' => $row->quantity ?? null,
            'uom' => $row->uom ?? null,
            'open_qty' => $row->open_qty ?? null,
            'line_remarks' => $row->line_remarks ?? null,
            'remarks' => $row->remarks ?? null,
        ];
    }

    /**
     * Convert SQL Server date to Y-m-d format
     *
     * @param mixed $date
     * @return string|null
     */
    private function convertSqlDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d');
            }

            if (is_string($date)) {
                $dateObj = new \DateTime($date);
                return $dateObj->format('Y-m-d');
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Error converting date: ' . $e->getMessage(), [
                'date' => $date,
            ]);
            return null;
        }
    }
}

