<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    //
    protected $fillable = [
        'order_number',
        'date_order',
        'shipping_date',
        'customer_id',
        'jenis_pembayaran_id',
        'shipping_address',
        'sales_person_id',
        'freight',
        'early_payment_terms',
        'messages'
    ];
    public function jenisPembayaran()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function details()
    {
        return $this->hasMany(SalesOrderDetail::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }

    public function salesPerson()
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }
}
