<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect; 
use App\Models\User;

class SubscriptionController extends Controller
{
    public function index(Request $request) 
    {
        $stripe = Cashier::stripe();
        $products = $stripe->products->all(); 
        $prices   = $stripe->prices->all();   
        $subscription = Auth::user()->subscriptions()->active()->latest()->first();
        return Inertia::render('Plans', [
            'products' => array_reverse($products->data), 
            'prices' => $prices->data,
            'subscription' => $subscription
        ]);
    }

    public function update(Request $request) 
    {
        $requestData = $request->all();
        if ($requestData["unit_amount"] === 0) {
            $this->subscribe($requestData["product"], $requestData["id"], Auth::user(), "Basic");
            return Redirect::route('plans.index');
        }
        dd($requestData);
        // $stripe = Cashier::stripe();
        // $products = $stripe->products->all(); 
        // $prices   = $stripe->prices->all();   
        
        // return Inertia::render('Plans', [
        //     'products' => array_reverse($products->data), 
        //     'prices' => $prices->data
        // ]);
    }

    protected function subscribe($prodId, $priceId, $user, $newSubscription) {;
        // $user->createOrGetStripeCustomer();
        $subscribed = $user->subscribed($newSubscription);
        
        if ($subscribed === null || $subscribed === false) {
            $stripe = Cashier::stripe();
            $products = $stripe->products->all();
            $currentProduct = array_values(array_filter($products->data, function ($prod) use ($prodId) {
                return $prod['id'] === $prodId;
            }))[0];
            $user->newSubscription($currentProduct['name'], $priceId)->add();
        }
        return true;
    }
}
