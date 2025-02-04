<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PrTemp;

class PRTempImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new PrTemp([
            'pr_draft_no' => $row['pr_draft_no'] ?? null,
            'pr_no' => $row['pr_no'] ?? null,
            'pr_date' => $this->convert_date($row['pr_date'] ?? null),
            'generated_date' => $this->convert_date($row['generated_date'] ?? null),
            'priority' => $row['priority'] ?? null,
            'pr_status' => $row['pr_status'] ?? null,
            'closed_status' => $row['closed_status'] ?? null,
            'pr_rev_no' => $row['pr_rev_no'] ?? null,
            'pr_type' => $row['pr_type'] ?? null,
            'project_code' => $row['project_code'] ?? null,
            'dept_name' => $row['dept_name'] ?? null,
            'for_unit' => $row['for_unit'] ?? null,
            'hours_meter' => $row['hours_meter'] ?? null,
            'required_date' => $this->convert_date($row['required_date'] ?? null),
            'requestor' => $row['requestor'] ?? null,
            'item_code' => $row['item_code'] ?? null,
            'item_name' => $row['item_name'] ?? null,
            'quantity' => $row['quantity'] ?? null,
            'uom' => $row['uom'] ?? null,
            'open_qty' => $row['open_qty'] ?? null,
            'line_remarks' => $row['line_remarks'] ?? null,
            'remarks' => $row['remarks'] ?? null,
        ]);
    }

    private function convert_date($date)
    {
        if ($date) {
            try {
                $year = substr($date, 6, 4);
                $month = substr($date, 3, 2);
                $day = substr($date, 0, 2);
                $new_date = $year . '-' . $month . '-' . $day;
                return $new_date;
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
