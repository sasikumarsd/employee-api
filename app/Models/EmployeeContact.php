<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Employee;

class EmployeeContact extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'phone'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

