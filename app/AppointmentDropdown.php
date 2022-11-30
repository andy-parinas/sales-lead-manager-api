<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentDropdown extends Model
{
    const OUTCOME = 'outcome';
    const CUSTOMER_TOUCH_POINT = 'customer_touch_point';

    protected $fillable = [
        'type',
        'name'
    ];
}
