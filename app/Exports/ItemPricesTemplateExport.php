<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemPricesTemplateExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return an empty collection with one example row
        return collect([
            [
                // Optional fields (can be provided in the upload form)
                'item_code' => 'ITEM001',
                'item_description' => 'Example Item Description',
                'part_number' => 'PART001',
                'brand' => 'Example Brand',

                // Required fields
                'uom' => 'PCS',
                'qty' => 10,
                'price' => 100000,
                'description' => 'Example description for the item',
            ]
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            // Optional fields (can be provided in the upload form)
            'item_code',
            'item_description',
            'part_number',
            'brand',

            // Required fields
            'uom',
            'qty',
            'price',
            'description',

            // Note about form fields
            'Form Fields (Reference Only)',
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        // Get supplier list for reference
        $suppliers = Supplier::select('id', 'name')->get();
        $supplierList = "The following fields are provided in the upload form:\n\n";
        $supplierList .= "- Supplier (required)\n";
        $supplierList .= "- Project (optional)\n";
        $supplierList .= "- Warehouse (optional)\n";
        $supplierList .= "- Start Date (optional)\n";
        $supplierList .= "- Expiry Date (optional)\n\n";
        $supplierList .= "Available suppliers:\n";

        foreach ($suppliers as $supplier) {
            $supplierList .= $supplier->id . ' - ' . $supplier->name . "\n";
        }

        return [
            $row['item_code'],
            $row['item_description'],
            $row['part_number'],
            $row['brand'],
            $row['uom'],
            $row['qty'],
            $row['price'],
            $row['description'],
            $supplierList,
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],

            // Add notes to the first row
            'A1:H1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => 'EEEEEE']]],

            // Set column widths
            'A' => ['width' => 15], // item_code
            'B' => ['width' => 30], // item_description
            'C' => ['width' => 15], // part_number
            'D' => ['width' => 15], // brand
            'E' => ['width' => 10], // uom
            'F' => ['width' => 10], // qty
            'G' => ['width' => 15], // price
            'H' => ['width' => 30], // description
            'I' => ['width' => 40], // Form Fields (Reference Only)
        ];
    }
}
