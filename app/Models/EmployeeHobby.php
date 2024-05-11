<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHobby extends Model
{
    use HasFactory;
    protected $table = 'employee_hobbies';

    public function hobby()
    {
        return $this->hasOne(Hobby::class,'id','hobby_id');
    }
}
