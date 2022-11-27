<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory,HasUuids;
    
    function user(){
        return $this->hasOne(Teacher::class);
    }
    function students(){
        return $this->hasMany(Student::class);
    }
    
}
