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
        'unit',
        'base_price',
        'tax_rate',
        'account_id',
        'is_active',
        'category_id',
        'brand',
        'stock_quantity',
        'purchase_price',
        'barcode',
        'image',
    ];

    public function account()
    {
        return $this->belongsTo(chartOfAccount::class, 'account_id');
    }

    public function category()
    {
        return $this->belongsTo(itemCategory::class, 'category_id');
    }
}
