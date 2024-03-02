<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'number',
        'date',
        'slip_number',
        'slip_date',
        'upload_copy',
        'employee_id',
    ];
}
