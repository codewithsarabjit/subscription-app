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
    public function index(Request $request, $success = null) 
    {
        $stripe = Cashier::stripe();
        $products = $stripe->products->all(); 
        $prices   = $stripe->prices->all();   
        $subscription = Auth::user()->subscriptions()->active()->latest()->first();
        return Inertia::render('Plans', [
            'products' => array_reverse($products->data), 
            'prices' => $prices->data,
            'subscription' => $subscription,
            'success' => $success
        ]);
    }

    public function update(Request $request) 
    {
        $requestData = $request->all();
        if ($requestData["unit_amount"] === 0) {
            $this->subscribe($requestData["product"], $requestData["id"], Auth::user(), "Basic");
            return Redirect::route('plans.index');
        }
        return Redirect::route('plans.pay', [
            'prodid' => $requestData["product"],
            'priceid' => $requestData["id"]
        ]);
    }

    public function pay($prodid, $priceid) 
    {
        $error = $_GET["error"] ?? "";
        return Inertia::render('Pay', [
            'prodid' => $prodid,
            'formUrl' => route('products.purchase', $prodid),
            'intent' => auth()->user()->createSetupIntent(),
            'token' => csrf_token(),
            'error' => $error
        ]);
    }

    public function purchase(Request $request, $productId)
    {
        $user          = $request->user();
        $paymentMethod = $request->input('payment_method');

        try {
            $stripe = Cashier::stripe();
            $prices = $stripe->prices->all(['product' => $productId]);
            $products = $stripe->products->all();
            $newProduct = array_values(array_filter($products->data, function ($prod) use ($productId) {
                return $prod['id'] === $productId;
            }))[0];
            
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);
            $user->charge($prices->data[0]->unit_amount, $paymentMethod);        
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            return Redirect::route('plans.pay', [
                'prodid' => $productId,
                'priceid' => $prices->data[0]->id,
                'error' => $exception->getMessage(),
            ]);
        }
        $user->newSubscription($newProduct->name, $prices->data[0]->id)->add();
        return Redirect::route('plans.index', [
            'success' => 'Product purchased successfully!',
        ]);
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
