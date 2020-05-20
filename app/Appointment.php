<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_date', 'appointment_notes', 'quoted_price', 'outcome', 'comments', 'lead_id'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function getDateString()
    {
        $dateArray = explode(" ", $this->appointment_date);

        if(count($dateArray) >= 2){
            return $dateArray[0];
        }

        return '';
    }

    public function getTimeString()
    {
        $dateArray = explode(" ", $this->appointment_date);
        if(count($dateArray) >= 2){
            return $dateArray[1];
        }

        return '';
    }
}
