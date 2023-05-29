<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\BeratingCalculation;
use App\Models\ColumbitePaymentAnalysis;
use App\Models\Manager;
use App\Models\Notification;
use App\Models\TinPaymentAnalysis;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
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

    function replaceCharsInNumber($num, $chars) 
    {
        return substr((string) $num, 0, -strlen($chars)) . $chars;
    }

    public function payment_analysis_tin_view()
    {
        return view('assistant_managers.payment_analysis_tin_view');
    }

    public function payment_analysis_tin_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('assistant_managers.payment_analysis_tin_add', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('assistant_managers.payment_analysis_tin_add', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('assistant_managers.payment_analysis_tin_add', compact('active_tab'));
        }
    }

    public function payment_analysis_tin_pound_post(Request $request)
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

            $wallet = Wallet::latest()->first();
    
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
                if($request->bag_pounds == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pounds' => ['required', 'numeric', 'max:69'],
                    ]);

                    $bag_pounds = $request->bag_pounds;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                ]);

                if($bag_pounds < pound_rate)
                {
                    $price_pound = $berating->unit_price;

                    $price_bag = $berating->price;

                    $equivalentPriceForBag = $request->bags * $price_bag;
                    $equivalentPriceForPound = $bag_pounds * $price_pound;
                    $total_in_pounds = ($request->bags * pound_rate) + $bag_pounds;

                    $totalPrice = $equivalentPriceForBag + $equivalentPriceForPound;

                    if($totalPrice > $wallet->amount)
                    {
                        $admin = User::where('account_type', 'Administrator')->first();

                        Notification::create([
                            'to' => $admin->id,
                            'title' => config('app.name'),
                            'body' => 'Wallet is low, please top up.'
                        ]);

                        return back()->with([
                            'type' => 'danger',
                            'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                        ]);
                    }

                    $filename = request()->receipt->getClientOriginalName();
                    request()->receipt->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = TinPaymentAnalysis::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'customer' => $request->customer,
                        'manager_id' => $manager->id,
                        'berating' => $request->berating,
                        'bags' => $request->bags,
                        'pounds' => $bag_pounds,
                        'bag_equivalent' => $equivalentPriceForBag,
                        'pound_equivalent' => $equivalentPriceForPound,
                        'total_in_pounds' => $total_in_pounds,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date' => $request->date,
                        'receipt' => '/storage/payment_analysis/'.$filename
                    ]);

                    $wallet->update([
                        'amount' => $wallet->amount - $tinPayment->price
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Voucher'
                    ]);

                    return redirect()->route('payment.analysis.tin.add', 'pound')->with([
                        'alertType' => 'success',
                        'message' => 'Payment Voucher created successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Pound should not be greater or equal to '.pound_rate
                    ]);
                }
            } 

            if($request->weight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);

                $price_pound = $berating->unit_price;

                $equivalentPriceForPound = $request->pounds * $price_pound;
                $total_in_pounds = $request->pounds;

                $totalPrice = $equivalentPriceForPound;

                if($totalPrice > $wallet->amount)
                {
                    $admin = User::where('account_type', 'Administrator')->first();

                    Notification::create([
                        'to' => $admin->id,
                        'title' => config('app.name'),
                        'body' => 'Wallet is low, please top up.'
                    ]);

                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                    ]);
                }

                $filename = request()->receipt->getClientOriginalName();
                request()->receipt->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = TinPaymentAnalysis::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'customer' => $request->customer,
                    'manager_id' => $manager->id,
                    'berating' => $request->berating,
                    'pounds' => $request->pounds,
                    'pound_equivalent' => $equivalentPriceForPound,
                    'total_in_pounds' => $total_in_pounds,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date' => $request->date,
                    'receipt' => '/storage/payment_analysis/'.$filename
                ]);

                $wallet->update([
                    'amount' => $wallet->amount - $tinPayment->price
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Voucher'
                ]);

                return redirect()->route('payment.analysis.tin.add', 'pound')->with([
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
            if($request->bag_pounds == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pounds' => ['required', 'numeric', 'max:69'],
                ]);

                $bag_pounds = $request->bag_pounds;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
            ]);

            if($bag_pounds < pound_rate)
            {
                $price_pound = $berating->unit_price;
                $price_bag = $berating->price;

                $equivalentPriceForBag = $request->bags * $price_bag;
                $equivalentPriceForPound = $request->bag_pounds * $price_pound;

                $totalPrice = $equivalentPriceForBag + $equivalentPriceForPound;

                return redirect()->route('payment.analysis.tin.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'Pound should not be greater or equal to '.pound_rate
                ]);
            }
        } 

        if($request->weight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $price_pound = $berating->unit_price;

            $equivalentPriceForPound = $request->pounds * $price_pound;

            $totalPrice = $equivalentPriceForPound;

            return redirect()->route('payment.analysis.tin.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_analysis_tin_kg_post(Request $request)
    {
        define("rate", 50);
        define("fixed_rate", 2.20462);
        
        if($request->save) 
        {
            $this->validate($request, [
                'customer' => ['required', 'string', 'max:255'],
                'berating' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date' => ['required', 'date'],
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->berating);

            $wallet = Wallet::latest()->first();
    
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

            if($request->kgweight == 'bag')
            {
                if($request->bag_kg == null)
                {
                    $bag_kgs = 0;
                } else {
                    $this->validate($request, [
                        'bag_kg' => ['required', 'numeric', 'max:49'],
                    ]);

                    $bag_kgs = $request->bag_kg;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $dollarRate = $analyses->dollar_rate;
                        $exchangeRate = $analyses->exchange_rate;
                    }
                }

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = $subTotal * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    if($totalPrice > $wallet->amount)
                    {
                        $admin = User::where('account_type', 'Administrator')->first();

                        Notification::create([
                            'to' => $admin->id,
                            'title' => config('app.name'),
                            'body' => 'Wallet is low, please top up.'
                        ]);

                        return back()->with([
                            'type' => 'danger',
                            'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                        ]);
                    }

                    $filename = request()->receipt->getClientOriginalName();
                    request()->receipt->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = TinPaymentAnalysis::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'customer' => $request->customer,
                        'manager_id' => $manager->id,
                        'berating' => $request->berating,
                        'bags' => $request->bags,
                        'kgs' => $bag_kgs,
                        'total_in_kg' => $subPrice,
                        'percentage_analysis' => $request->percentage,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date' => $request->date,
                        'receipt' => '/storage/payment_analysis/'.$filename
                    ]);

                    $wallet->update([
                        'amount' => $wallet->amount - $tinPayment->price
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Voucher'
                    ]);
    
                    return redirect()->route('payment.analysis.tin.add', 'kg')->with([
                        'alertType' => 'success',
                        'message' => 'Payment Voucher created successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.rate
                    ]);
                }
            } 

            if($request->kgweight == 'kg')
            {
                $this->validate($request, [
                    'kg' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $dollarRate = $analyses->dollar_rate;
                        $exchangeRate = $analyses->exchange_rate;
                    }
                }
    
                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = $subTotal * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if($totalPrice > $wallet->amount)
                {
                    $admin = User::where('account_type', 'Administrator')->first();

                    Notification::create([
                        'to' => $admin->id,
                        'title' => config('app.name'),
                        'body' => 'Wallet is low, please top up.'
                    ]);

                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                    ]);
                }

                $filename = request()->receipt->getClientOriginalName();
                request()->receipt->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = TinPaymentAnalysis::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'customer' => $request->customer,
                    'manager_id' => $manager->id,
                    'berating' => $request->berating,
                    'kgs' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'percentage_analysis' => $request->percentage,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date' => $request->date,
                    'receipt' => '/storage/payment_analysis/'.$filename
                ]);

                $wallet->update([
                    'amount' => $wallet->amount - $tinPayment->price
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Voucher'
                ]);

                return redirect()->route('payment.analysis.tin.add', 'kg')->with([
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
       
        if($request->kgweight == 'bag')
        {
            if($request->bag_kg == null)
            {
                $bag_kgs = 0;
            } else {
                $this->validate($request, [
                    'bag_kg' => ['required', 'numeric', 'max:49'],
                ]);

                $bag_kgs = $request->bag_kg;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $dollarRate = $analyses->dollar_rate;
                    $exchangeRate = $analyses->exchange_rate;
                }
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = $subTotal * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.analysis.tin.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'kg should not be greater or equal to '.rate
                ]);
            }
            
        } 

        if($request->kgweight == 'kg')
        {
            $this->validate($request, [
                'kg' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $dollarRate = $analyses->dollar_rate;
                    $exchangeRate = $analyses->exchange_rate;
                }
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = $subTotal * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.analysis.tin.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
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

    public function payment_analysis_columbite_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('assistant_managers.payment_analysis_columbite_add', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('assistant_managers.payment_analysis_columbite_add', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('assistant_managers.payment_analysis_columbite_add', compact('active_tab'));
        }
    }

    public function payment_analysis_columbite_pound_post(Request $request)
    {
        define("columbite_rate", 80);
        
        if($request->save) 
        {
            $this->validate($request, [
                'customer' => ['required', 'string', 'max:255'],
                'berating' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date' => ['required', 'date'],
                'receipt' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->berating);

            $wallet = Wallet::latest()->first();
    
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

            if($request->poundweight == 'bag')
            {
                if($request->bag_pound == null)
                {
                    $bag_pounds = 0;
                } else {
                    $this->validate($request, [
                        'bag_pound' => ['required', 'numeric', 'max:79'],
                    ]);

                    $bag_pounds = $request->bag_pound;
                }

                $this->validate($request, [
                    'bags' => ['required', 'numeric'],
                    'percentage' => ['required', 'numeric', 'min:25'],
                ]);

                $analysis = AnalysisCalculation::get();

                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $dollarRate = $analyses->dollar_rate;
                        $exchangeRate = $analyses->exchange_rate;
                    }
                }

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = $subTotal * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');

                    if($totalPrice > $wallet->amount)
                    {
                        $admin = User::where('account_type', 'Administrator')->first();

                        Notification::create([
                            'to' => $admin->id,
                            'title' => config('app.name'),
                            'body' => 'Wallet is low, please top up.'
                        ]);

                        return back()->with([
                            'type' => 'danger',
                            'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                        ]);
                    }
                    
                    $filename = request()->receipt->getClientOriginalName();
                    request()->receipt->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment = ColumbitePaymentAnalysis::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'customer' => $request->customer,
                        'manager_id' => $manager->id,
                        'berating' => $request->berating,
                        'bags' => $request->bags,
                        'pounds' => $bag_pounds,
                        'total_in_pounds' => $subPrice,
                        'percentage_analysis' => $request->percentage,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date' => $request->date,
                        'receipt' => '/storage/payment_analysis/'.$filename
                    ]);

                    $wallet->update([
                        'amount' => $wallet->amount - $columbitePayment->price
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $columbitePayment->id,
                        'amount' => $columbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Voucher'
                    ]);

                    return redirect()->route('payment.analysis.columbite.add', 'pound')->with([
                        'alertType' => 'success',
                        'message' => 'Payment Voucher created successfully'
                    ]);
                } else {
                    return back()->with([
                        'type' => 'danger',
                        'message' => 'kg should not be greater or equal to '.columbite_rate
                    ]);
                }
            } 

            if($request->poundweight == 'pound')
            {
                $this->validate($request, [
                    'pounds' => ['required', 'numeric']
                ]);
    
                $analysis = AnalysisCalculation::get();
    
                foreach($analysis as $analyses)
                {
                    if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                    {
                        $dollarRate = $analyses->dollar_rate;
                        $exchangeRate = $analyses->exchange_rate;
                    }
                }
    
                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = $subTotal * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                if($totalPrice > $wallet->amount)
                {
                    $admin = User::where('account_type', 'Administrator')->first();

                    Notification::create([
                        'to' => $admin->id,
                        'title' => config('app.name'),
                        'body' => 'Wallet is low, please top up.'
                    ]);

                    return back()->with([
                        'type' => 'danger',
                        'message' => 'Wallet is low, please contact the administrator to top up and try again!'
                    ]);
                }

                $filename = request()->receipt->getClientOriginalName();
                request()->receipt->storeAs('payment_analysis', $filename, 'public');

                $columbitePayment = ColumbitePaymentAnalysis::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'customer' => $request->customer,
                    'manager_id' => $manager->id,
                    'berating' => $request->berating,
                    'pounds' => $request->pounds,
                    'total_in_pounds' => $request->pounds,
                    'percentage_analysis' => $request->percentage,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date' => $request->date,
                    'receipt' => '/storage/payment_analysis/'.$filename
                ]);

                $wallet->update([
                    'amount' => $wallet->amount - $columbitePayment->price
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $columbitePayment->id,
                    'amount' => $columbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Voucher'
                ]);

                return redirect()->route('payment.analysis.columbite.add', 'pound')->with([
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
       
        if($request->poundweight == 'bag')
        {
            if($request->bag_pound == null)
            {
                $bag_pounds = 0;
            } else {
                $this->validate($request, [
                    'bag_pound' => ['required', 'numeric', 'max:79'],
                ]);

                $bag_pounds = $request->bag_pound;
            }

            $this->validate($request, [
                'bags' => ['required', 'numeric'],
                'percentage' => ['required', 'numeric', 'min:25'],
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $dollarRate = $analyses->dollar_rate;
                    $exchangeRate = $analyses->exchange_rate;
                }
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = $subTotal * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.analysis.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return back()->with([
                    'type' => 'danger',
                    'message' => 'pound should not be greater or equal to '.columbite_rate
                ]);
            }
            
        } 

        if($request->poundweight == 'pound')
        {
            $this->validate($request, [
                'pounds' => ['required', 'numeric']
            ]);

            $analysis = AnalysisCalculation::get();

            foreach($analysis as $analyses)
            {
                if($request->percentage >= $analyses->percentage_min && $request->percentage <= $analyses->percentage_max)
                {
                    $dollarRate = $analyses->dollar_rate;
                    $exchangeRate = $analyses->exchange_rate;
                }
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = $subTotal * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.analysis.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return back()->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }
}
