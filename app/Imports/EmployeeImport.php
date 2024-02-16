<?php

namespace App\Imports;

use App\Models\Empdetail;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class EmployeeImport implements ToModel, WithHeadingRow
{
    protected $customerId;
    protected $cname;
    protected $cadd;

    public function __construct($customerId, $cname, $cadd)
    {
        $this->customerId = $customerId;
        $this->cname = $cname;
        $this->cadd = $cadd;
    }

    public function model(array $row)
    {

        // $sname = isset($row['sname']) ? $row['sname'] : null;
        // Generate sname
        $cnameWords = explode(" ", $this->cname);
        $caddWords = explode(" ", $this->cadd);
        $initials = "";
        foreach ($cnameWords as $w) {
            $initials .= $w[0];
        }
        $initials .= $caddWords[0][0];

        $sname = strtoupper($initials) . $this->customerId;
        $token = isset($row['tokenno']) ? $row['tokenno'] : null;
        $fullname = isset($row['fullname']) ? $row['fullname'] : null;
        $category = isset($row['category']) ? $row['category'] : null;
        $remarks = isset($row['remarks']) ? $row['remarks'] : null;
        $setOrder = isset($row['setorder']) ? $row['setorder'] : null;
        // $status = isset($row['status']) ? $row['status'] : null;
        $status = "MP";

        return new Empdetail([
            'sname' => $sname,
            'tokenNo' => $token,
            'fullName' => $fullname,
            'category' => $category,
            'remarks' => $remarks,
            'setOrder' => $setOrder,
            'status' => $status,
            'customer_id' => $this->customerId,
        ]);
    }
}
