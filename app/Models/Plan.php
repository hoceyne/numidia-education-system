<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory,HasUuids;

    protected $fillable = [
        'price',
        'duration',
        'benefits',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function departements(){
        return $this->hasMany(Departement::class);
    }

    public function clients()
    {
        return $this->hasMany(Student::class);
    }
    
}
