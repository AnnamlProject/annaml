<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    //
    protected $fillable = [
        'invoice_number',
        'date_invoice',
        'purchase_order_id',
        'jenis_pembayaran_id',
        'customer_id',
        'shipping_date',
        'shipping_address',
        'freight',
        'early_payment_terms',
        'messages'
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
        return $this->hasMany(PurchaseInvoiceDetail::class);
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function documents()
    {
        return $this->hasMany(PurchaseInvoiceDocument::class);
    }
}
