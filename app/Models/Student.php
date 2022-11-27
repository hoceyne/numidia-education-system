<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory,HasUuids;

    function user(){
        return $this->belongsTo(User::class);
    }
    function plan(){
        return $this->belongsTo(Plan::class);
    }
    function sessions(){
        return $this->belongsToMany(Session::class,'session_students','student_id','session_id');
    }
    function supervisor(){
        return $this->belongsTo(Supervisor::class);
    }
}
