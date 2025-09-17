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
        'vendor_id',
        'account_id',
        'freight',
        'early_payment_terms',
        'messages',
    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
    public function documents()
    {
        return $this->hasMany(PurchaseOrderDocument::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendors::class);
    }
    public function paymentMethodDetail()
    {
        return $this->belongsTo(PaymentMethodDetail::class);
    }
}
