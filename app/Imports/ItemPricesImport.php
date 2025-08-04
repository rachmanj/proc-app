<?php

namespace App\Imports;

use App\Models\ItemPriceImport;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ItemPricesImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $options;

    public function __construct($options)
    {
        if (is_array($options)) {
            $this->options = $options;
        } else {
            // For backward compatibility
            $this->options = [
                'batch_id' => $options,
                'supplier_id' => null,
                'project' => null,
                'warehouse' => null,
                'start_date' => now()->format('Y-m-d'),
                'expired_date' => null,
            ];
        }
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Get supplier ID from options or row
            $supplierId = $this->options['supplier_id'] ?? $row['supplier_id'] ?? null;

            // Get other values with form values as defaults
            $itemCode = $row['item_code'] ?? null;
            $itemDescription = $row['item_description'] ?? null;
            $partNumber = $row['part_number'] ?? null;
            $brand = $row['brand'] ?? null;
            $project = $row['project'] ?? $this->options['project'] ?? null;
            $warehouse = $row['warehouse'] ?? $this->options['warehouse'] ?? null;
            $startDate = $this->transformDate($row['start_date'] ?? null) ?? $this->options['start_date'] ?? now()->format('Y-m-d');
            $expiredDate = $this->transformDate($row['expired_date'] ?? null) ?? $this->options['expired_date'] ?? null;

            // Required fields validation
            $validator = Validator::make([
                'supplier_id' => $supplierId,
                'uom' => $row['uom'] ?? null,
                'qty' => $row['qty'] ?? null,
                'price' => $row['price'] ?? null,
            ], [
                'supplier_id' => 'required|exists:suppliers,id',
                'uom' => 'required|string',
                'qty' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
            ]);

            // Create import record
            $importItem = new ItemPriceImport([
                'supplier_id' => $supplierId,
                'item_code' => $itemCode,
                'item_description' => $itemDescription,
                'part_number' => $partNumber,
                'brand' => $brand,
                'project' => $project,
                'warehouse' => $warehouse,
                'start_date' => $startDate,
                'expired_date' => $expiredDate,
                'uom' => $row['uom'] ?? null,
                'qty' => $row['qty'] ?? null,
                'price' => $row['price'] ?? null,
                'description' => $row['description'] ?? null,
                'import_batch' => $this->options['batch_id'],
                'status' => $validator->fails() ? 'error' : 'pending',
                'error_message' => $validator->fails() ? json_encode($validator->errors()->toArray()) : null,
                'uploaded_by' => Auth::id(),
            ]);

            $importItem->save();
        }
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Transform a date value from the Excel file.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Handle Excel date format
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Handle string date format
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
