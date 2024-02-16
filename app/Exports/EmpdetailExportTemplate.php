<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmpdetailExportTemplate implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Return an empty collection
        return collect([]);
    }

    public function headings(): array
    {
        return [
            // 'sname',
            'tokenNo',
            'fullName',
            'category',
            'remarks',
            'setOrder',
            // 'status',
        ];
    }
}