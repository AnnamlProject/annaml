<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    //

    protected $fillable = [
        'order_number',
        'date_order',
        'shipping_address',
        'shipping_date',
        'jenis_pembayaran_id',
        'customer_id',
        'freight',
        'early_payment_terms',
        'messages',
    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
}
