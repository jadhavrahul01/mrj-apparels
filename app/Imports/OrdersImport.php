<?php

namespace App\Imports;

use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

    
        return new Order([
            // 'id'     => $row[0],
            'order_id'    => $row[1],
            'u_id' => $row[2],
            'cname' => $row[3],
            'cadd' => $row[4],
            'cgstin' => $row[5],
            'cstyle_ref' => $row[6],
            'email' => $row[7],
            'phone' => $row[8],
            'mtaker' => $row[9] ?? null,
            'ponumber' => $row[14],
            'poimg' => $row[15],
            'remark' => $row[12],
            'fabrics_status' => $row[17] ?? 0,
            'status' => $row[18] ?? 0,
            'created_at' => $row[20] ?? null, 
            'updated_at' => $row[21] ?? null, 

        ]);
    }

}
