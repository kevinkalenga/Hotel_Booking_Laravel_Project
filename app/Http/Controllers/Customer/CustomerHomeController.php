<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class CustomerHomeController extends Controller
{
    public function index()
    {
        $total_completed_orders = Order::where('status', 'Completed')->count();
        $total_pending_orders = Order::where('status', 'Pending')->count();
        return view('customer.home', compact('total_completed_orders', 'total_pending_orders'));
    }
}
