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
        'shipping_date',
        'shipping_address',
        'freight',
        'early_payment_terms',
        'messages',
        'vendor_id',
        'account_id',
        'location_id',
        'status_purchase',

    ];

    public function jenisPembayaran()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendors::class);
    }
    public function details()
    {
        return $this->hasMany(PurchaseInvoiceDetail::class, 'purchase_invoice_id');
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
    public function documents()
    {
        return $this->hasMany(PurchaseInvoiceDocument::class);
    }
    public function locationInventories()
    {
        return $this->belongsTo(LocationInventory::class, 'location_id');
    }
    public function prepaymentAllocations()
    {
        return $this->hasMany(PrepaymentAllocation::class);
    }
}
