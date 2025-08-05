<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    //
    protected $fillable = [
        'sales_order_id',
        'item_id',
        'item_description',
        'quantity',
        'back_order',
        'unit',
        'base_price',
        'discount',
        'price',
        'amount',
        'tax',
        'account_id',
        'order'
    ];
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function account()
    {
        return $this->belongsTo(chartOfAccount::class);
    }
}
