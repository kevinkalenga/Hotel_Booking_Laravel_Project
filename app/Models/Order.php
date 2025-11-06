<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = []; // Allows mass assignment (optional but useful)

    /**
     * Get the customer who placed this order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get all details (rooms) associated with this order.
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}

