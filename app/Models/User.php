<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'role',
        'gender',
        'google_id',
        'facebook_id',
        'code',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function admin(){
        return $this->hasOne(Admin::class);
    }
    function student(){
        return $this->hasOne(Student::class);
    }
    function teacher(){
        return $this->hasOne(Teacher::class);
    }
    function supervisor(){
        return $this->hasOne(Supervisor::class);
    }
    function received_notifications(){
        return $this->hasMany(Notification::class,'from');
    }
    function sent_notifications(){
        return $this->hasMany(Notification::class,'to');
    }
    public function profile_picture(){
        return $this->hasOne(File::class);
    }

}
