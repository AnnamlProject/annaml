<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemBuild extends Model
{
    //
    protected $fillable = [
        'item_id',
        'build_quantity',
        'additional_costs',
        'cost_account',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function components()
    {
        return $this->hasMany(InventoryBuildComponent::class);
    }
}
