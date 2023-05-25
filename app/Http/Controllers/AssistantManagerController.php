<?php

namespace App\Http\Controllers;

use App\Models\BeratingCalculation;
use App\Models\Manager;
use App\Models\TinPaymentAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantManagerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function payment_analysis_tin_view()
    {
        return view('assistant_managers.payment_analysis_tin_view');
    }

    public function payment_analysis_tin_add()
    {
        return view('assistant_managers.payment_analysis_tin_add');
    }

    public function payment_analysis_tin_post(Request $request)
    {
        define("pound_rate", 70);
        
        if($request->save) 
        {
            $this->validate($request, [
                'customer' => ['required', 'string', 'max:255'],
                'berating' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date' => ['required', 'date'],
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->berating);
    
            if(!$berating)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $manager = Manager::find($request->manager);

            if(!$manager)
            {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Manager not found in our database.'
                ]); 
            }

            if($request->weight == 'bag')
            {
                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'bag_pounds' => ['string', 'numeric', 'max:69'],
                ]);

                if($request->bag_pounds < pound_rate)
                {
                    $price_pound = $berating->unit_price;
                }

                $price_bag = $berating->price;

                $equivalentPriceForBag = $request->bags * $price_bag;
                $equivalentPriceForPound = $request->bag_pounds * $price_pound;

                $totalPrice = $equivalentPriceForBag + $equivalentPriceForPound;

                $filename = request()->receipt->getClientOriginalName();
                request()->receipt->storeAs('payment_analysis', $filename, 'public');

                TinPaymentAnalysis::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'customer' => $request->customer,
                    'manager_id' => $manager->id,
                    'berating' => $request->berating,
                    'bags' => $request->bags,
                    'pounds' => $request->bag_pounds,
                    'bag_equivalent' => $equivalentPriceForBag,
                    'pound_equivalent' => $equivalentPriceForPound,
                    'price' => $totalPrice,
                    'date' => $request->date,
                    'receipt' => '/storage/payment_analysis/'.$filename
                ]);

                return back()->with([
                    'alertType' => 'success',
                    'message' => 'Payment Voucher created successfully'
                ]);
            } 

            if($request->weight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);

                $price_pound = $berating->unit_price;

                $equivalentPriceForPound = $request->pounds * $price_pound;

                $totalPrice = $equivalentPriceForPound;

                $filename = request()->receipt->getClientOriginalName();
                request()->receipt->storeAs('payment_analysis', $filename, 'public');

                TinPaymentAnalysis::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'customer' => $request->customer,
                    'manager_id' => $manager->id,
                    'berating' => $request->berating,
                    'pounds' => $request->pounds,
                    'pound_equivalent' => $equivalentPriceForPound,
                    'price' => $totalPrice,
                    'date' => $request->date,
                    'receipt' => '/storage/payment_analysis/'.$filename
                ]);

                return back()->with([
                    'alertType' => 'success',
                    'message' => 'Payment Voucher created successfully'
                ]);
            } 

            return back()->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'berating' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->berating);

        if(!$berating)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add this berating value, try again later.'
            ]); 
        }
       
        if($request->weight == 'bag')
        {
            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'bag_pounds' => ['string', 'numeric', 'max:69'],
            ]);

            if($request->bag_pounds < pound_rate)
            {
                $price_pound = $berating->unit_price;
            }

            $price_bag = $berating->price;

            $equivalentPriceForBag = $request->bags * $price_bag;
            $equivalentPriceForPound = $request->bag_pounds * $price_pound;

            $totalPrice = $equivalentPriceForBag + $equivalentPriceForPound;

            // return redirect()->route('payment.analysis.tin.add', $totalPrice);
            return back()->with([
                'previewPrice' => 'success',
                'message' => $totalPrice
            ]);
        } 

        if($request->weight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $price_pound = $berating->unit_price;

            $equivalentPriceForPound = $request->pounds * $price_pound;

            $totalPrice = $equivalentPriceForPound;

            // return redirect()->route('payment.analysis.tin.add', $totalPrice);
            return back()->with([
                'previewPrice' => 'success',
                'message' => $totalPrice
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_analysis_columbite_view()
    {
        return view('assistant_managers.payment_analysis_columbite_view');
    }

    public function payment_analysis_columbite_add()
    {
        return view('assistant_managers.payment_analysis_columbite_add');
    }
}
