<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    const INSIDE_OF_FRANCHISE = 'inside_of_franchise';
    const OUTSIDE_OF_FRANCHISE = 'outside_of_franchise';

    protected $fillable = [
        'lead_number',
        'branch_id',
        'sales_contact_id',
        'lead_source_id',
        'lead_date',
        'postcode_status',
        'franchise_id'
    ];


    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function salesContact()
    {
        return $this->belongsTo(SalesContact::class);
    }


    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class);
    }

    public function jobType()
    {
        return $this->hasOne(JobType::class);
    }

    public function appointment()
    {
        return $this->hasOne(Appointment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function finance()
    {
        return $this->hasOne(Finance::class);
    }

}
