<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaimDropdown extends Model
{
    protected $fillable = [
        'type',
        'description',
    ];
}
