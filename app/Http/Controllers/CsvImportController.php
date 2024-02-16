<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Empdetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Exports\EmpdetailExport;
use App\Exports\EmpdetailExportTemplate;
use Illuminate\Support\Facades\File;


class CsvImportController extends Controller
{
    public function import(Request $request)
    {
        request()->validate([
            'excel' => 'required',
            'cusid' => 'required',
            'cname' => 'required',
            'cadd' => 'required',
        ]);
        $customerId = $request->cusid;
        $cname = $request->cname;
        $cadd = $request->cadd;
        Excel::import(new EmployeeImport($customerId, $cname, $cadd), $request->file('excel'));
        return back()->with('massage', 'User Imported Successfully');
    }


    public function export(Request $request)
    {
        $customerId = $request->customerId;
        return Excel::download(new EmpdetailExport($customerId), "empdetails_{$customerId}.csv");
    }

    public function exportTemplate()
    {
        return Excel::download(new EmpdetailExportTemplate(), "empdetails_template.csv");
    }

    // public function downloadEmployeeTemplate()
    // {
    //     $path = storage_path('app/public/employee_template/employee_template.csv');
    //     if (!File::exists($path)) {
    //         return response()->json(['error' => 'File not found'], 404);
    //     }

    //     return response()->download($path, 'employee_template.csv');
    // }
}
