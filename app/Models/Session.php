<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory,HasUuids;
    
    protected $fillable = [
        'classroom',
        'starts_at',
        'ends_at',
        'state',
    ];


    function teacher(){
        return $this->belongsTo(Teacher::class);
    }
    function students(){
        return $this->belongsToMany(Student::class, 'session_student', 'session_id', 'student_id');
    }
    
}
