<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $fillable = [
        'item_number',
        'item_name',
        'item_description',
        'description',
        'type',
        'picture_path',
        'thubmnail_path',
    ];
    public function quantities()
    {
        return $this->hasMany(ItemQuantities::class);
    }

    public function units()
    {
        return $this->hasOne(ItemUnit::class);
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function vendors()
    {
        return $this->hasMany(ItemVendor::class);
    }

    public function accounts()
    {
        return $this->hasOne(ItemAccount::class);
    }

    public function builds()
    {
        return $this->hasMany(ItemBuild::class);
    }

    public function taxes()
    {
        return $this->hasMany(ItemTaxes::class);
    }
}
