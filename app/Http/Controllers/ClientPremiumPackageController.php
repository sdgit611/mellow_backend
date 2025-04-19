<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Premium;
use App\Models\developerPayments;
use App\Models\developerPremiumPrice;
use App\Http\Requests\PremiumPayRequest;

class ClientPremiumPackageController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.buy_premium_package';
       
    }

    public function index()
    {
        $this->premium = Premium::all();
        
        $this->prices = developerPremiumPrice::where('status',true)->get();

        return view('client.payment.index', $this->data);
    }

    public function PremiumPay(PremiumPayRequest $request)
    {
        $plan = DeveloperPremiumPrice::find($request->razorpay_id);
    
        if (!$plan) {
            return response()->json(['error' => 'Invalid plan selected.'], 404);
        }
    
        $expiryDate = match ($plan->name) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => null, // 'one time' or any undefined
        };
    
        developerPayments::create([
            'developer_id' => $developerId,
            'payment_id' => $request->razorpay_payment_id,
            'order_id' => $request->razorpay_order_id,
            'signature' => $request->razorpay_signature,
            'developer_premium_prices_id' => $plan->id,
            'expired' => $expiryDate,
        ]);
    
        return response()->json(['success' => true, 'message' => 'Payment recorded successfully.']);
    }

    public function paymentSuccess(Request $request)
    {
        // Save the payment info if needed
        return response()->json(['success' => true, 'message' => 'Payment successful!']);
    }
}
