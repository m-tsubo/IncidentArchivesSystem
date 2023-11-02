<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{

    use HasFactory;

    protected $fillable = [
    'case_name', 'detail_path', 'order_number', 'person_in_charge', 'department_id', 'incident', 'solution', 'user_id'
];
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    
    public function user()
{
    return $this->belongsTo(User::class);
}
}
