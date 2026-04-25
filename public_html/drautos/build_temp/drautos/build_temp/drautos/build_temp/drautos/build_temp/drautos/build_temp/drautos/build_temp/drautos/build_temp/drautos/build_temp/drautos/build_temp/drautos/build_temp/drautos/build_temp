<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','photo','status','provider','provider_id','phone','address','city','shipping_address','shipping_city',
        'courier_company', 'courier_number', 'base_salary', 'overtime_rate', 'customer_type', 'rating'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function orders(){
        return $this->hasMany('App\Models\Order');
    }

    public function payments() {
        return $this->hasMany('App\Models\EmployeePayment', 'employee_id');
    }

    public function advances() {
        return $this->hasMany('App\Models\EmployeeAdvance', 'employee_id');
    }

    public function commissions() {
        return $this->hasMany('App\Models\EmployeeCommission', 'employee_id');
    }

    public function paymentReminders() {
        return $this->morphMany('App\Models\PaymentReminder', 'party');
    }
}
