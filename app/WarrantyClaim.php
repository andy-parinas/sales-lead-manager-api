<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'date_complaint',
        'complaint_received',
        'complaint_type',
        'home_addition_type',
        'complaint_description',
        'contacted_franchise',
        'status',
        'lead_id'
    ];
    
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
