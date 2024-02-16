<?php

namespace App\Exports;

use App\Models\Empdetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpdetailExport implements FromCollection, WithHeadings
{
    private $customerId;

    public function __construct(int $customerId)
    {
        $this->customerId = $customerId;
    }

    public function collection()
    {
        return Empdetail::select('sname', 'tokenNo', 'fullName', 'category', 'setOrder', 'status')
            ->where('customer_id', $this->customerId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'sname',
            'tokenNo',
            'fullName',
            'category',
            'setOrder',
            'status',
        ];
    }
}