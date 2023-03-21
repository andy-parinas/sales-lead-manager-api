<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postcode extends Model
{

    use SoftDeletes;

    protected $fillable = ['pcode', 'locality', 'state'];



    public function franchises()
    {
        return $this->belongsToMany(Franchise::class);
    }

    public function salesContacts()
    {
        return $this->hasMany(SalesContact::class);
    }

    public function constructions()
    {
        return $this->hasMany(Construction::class);
    }

}
