<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory,HasUuids;
    
    protected $fillable = [
        'departement',
        'school',
        'sepciality',
        'level',

    ];

    protected $keyType = 'string';
    public $incrementing = false;

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    function students(){
        return $this->hasMany(Group::class);
    }
}
