<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Order::all();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID',
            'Order ID',
            'User ID',
            'Customer Name',
            'Customer Address',
            'Customer GSTIN',
            'Customer Style Ref',
            'Email',
            'Phone',
            'Material Taker',
            'PO Number',
            'PO Image',
            'Remark',
            'Fabrics Status',
            'Status',
            'Created At',
            'Updated At',
        ];
    }
}