<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable=['short_des','description','photo','address','phone','email','logo','terms_urdu','terms_english'];
}
