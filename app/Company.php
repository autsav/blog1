<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
