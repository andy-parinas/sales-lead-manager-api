<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;

class SalesContact extends Model
{
    use SearchableTrait;

    const RESIDENTIAL = 'residential';
    const COMMERCIAL = 'commercial';

    const ACTIVE = 'active';
    const ARCHIVED = 'archived';

    protected $fillable = [
        'title', 'first_name', 'last_name', 'email', 'email2',
        'contact_number', 'street1', 'street2', 'postcode_id', 'customer_type', 'status'
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'sales_contacts.first_name' => 10,
            'sales_contacts.last_name' => 10
        ]
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTitledFullNameAttribute()
    {
        return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function postcode()
    {
        return $this->belongsTo(Postcode::class);
    }

}
