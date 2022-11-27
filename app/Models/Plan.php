<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'duration',
        'benefits',
    ];


    public function clients()
    {
        return $this->hasMany(Student::class);
    }
}
