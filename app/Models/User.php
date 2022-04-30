<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'isAdmin'
    ];

    protected $attributes = [
        'isAdmin' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
    public function orders()
    {
        return $this->hasManyThrough(Order::class, Job::class);
    }
    public function hired()
    {
        return $this->hasMany(Order::class,'buyer_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function messages()
    {
        return $this->hasMany(Notification::class,'from');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Job::class);
    }

   
}
