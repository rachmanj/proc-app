<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PoTemp;

class POTempImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new PoTemp([
            'po_no' => $row['po_no'] ?? null,
            'posting_date' => $this->convert_date($row['posting_date']),
            'create_date' => $this->convert_date($row['create_date']),
            'po_delivery_date' => $this->convert_date($row['po_delivery_date']),
            'po_eta' => $this->convert_date($row['po_eta']),
            'pr_no' => $row['pr_no'] ?? null,
            'vendor_code' => $row['vendor_code'] ?? null,
            'vendor_name' => $row['vendor_name'] ?? null,
            'unit_no' => $row['unit_no'] ?? null,
            'item_code' => $row['item_code'] ?? null,
            'description' => $row['description'] ?? null,
            'qty' => $row['qty'] ?? null,
            'po_currency' => $row['po_currency'] ?? null,
            'unit_price' => $row['unit_price'] ?? null,
            'item_amount' => $row['item_amount'] ?? null,
            'total_po_price' => $row['total_po_price'] ?? null,
            'po_with_vat' => $row['po_with_vat'] ?? null,
            'uom' => $row['uom'] ?? null,
            'project_code' => $row['project_code'] ?? null,
            'dept_code' => $row['dept_code'] ?? null,
            'po_status' => $row['po_status'] ?? null,
            'po_delivery_status' => $row['po_delivery_status'] ?? null,
            'budget_type' => $row['budget_type'] ?? null,
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
