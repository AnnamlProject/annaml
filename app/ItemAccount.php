<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemAccount extends Model
{
    //
    protected $fillable = [
        'item_id',
        'asset_account_id',
        'revenue_account_id',
        'cogs_account_id',
        'variance_account_id',
        'expense_account_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function account()
    {
        return $this->belongsTo(chartOfAccount::class);
    }
}
