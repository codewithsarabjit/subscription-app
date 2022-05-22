<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class SubscriptionController extends Controller
{
    public function plans() 
    {
        $stripe = Cashier::stripe();
        $products = $stripe->products->all(); 
        $prices   = $stripe->prices->all();   
        
        return Inertia::render('Plans', [
            'products' => array_reverse($products->data), 
            'prices' => $prices->data
        ]);
    }
}
