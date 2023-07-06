<?php

namespace App\Http\Controllers;

use App\Models\AnalysisCalculation;
use App\Models\Benchmark;
use App\Models\BeratingCalculation;
use App\Models\PaymentReceiptColumbite;
use App\Models\PaymentReceiptLowerGradeColumbite;
use App\Models\PaymentReceiptTin;
use App\Models\Transaction;
use App\Models\User;
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
        define("pound_rate", 70);
        define("rate", 50);
        define("fixed_rate", 2.20462);
        define("columbite_rate", 80);
    }

    function replaceCharsInNumber($num, $chars) 
    {
        return substr((string) $num, 0, -strlen($chars)) . $chars;
    }

    public function payment_receipt_tin_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $tinPaymentReceiptPound = PaymentReceiptTin::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $tinPaymentReceiptKg = PaymentReceiptTin::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_tin_view', [
                    'tinPaymentReceiptKg' => $tinPaymentReceiptKg,
                    'tinPaymentReceiptPound' => $tinPaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }
    }

    public function payment_receipt_tin_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('assistant_managers.payment_receipt_tin_add', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('assistant_managers.payment_receipt_tin_add', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('assistant_managers.payment_receipt_tin_add', compact('active_tab'));
        }
    }

    public function payment_receipt_tin_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
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
                    $price_pound = $berating->price / pound_rate;
                    $price_bag = $berating->price;

                    $equivalentPriceForBag = $request->bags * $price_bag;
                    $equivalentPriceForPound = $bag_pounds * $price_pound;
                    $total_in_pounds = ($request->bags * pound_rate) + $bag_pounds;

                    $total = $equivalentPriceForBag + $equivalentPriceForPound;

                    $totalPrice = number_format((float)$total, 0, '.', '');

                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = PaymentReceiptTin::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $total_in_pounds,
                        'berating_rate_list' => $berate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.tin.view', 'pound'),
                        'message' => 'Payment Receipt created successfully'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.tin.add', 'pound')->with([
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

                $equivalentPriceForPound = $request->pounds * $berating->unit_price;

                $total_in_pounds = $request->pounds;

                $total = $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = PaymentReceiptTin::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $total_in_pounds,
                    'berating_rate_list' => $berate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.tin.view', 'pound'),
                    'message' => 'Payment Receipt created successfully'
                ]);
            } 

            return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.tin.add', 'pound')->with([
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
                $price_pound = $berating->price / pound_rate;
                $price_bag = $berating->price;

                $equivalentPriceForBag = $request->bags * $price_bag;
                $equivalentPriceForPound = $request->bag_pounds * $price_pound;

                $total = $equivalentPriceForBag + $equivalentPriceForPound;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
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

            $equivalentPriceForPound = $request->pounds * $berating->unit_price;

            $total = $equivalentPriceForPound;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.tin.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_tin_kg_post(Request $request)
    {
        $benchmark = Benchmark::latest()->first();

        if(!$benchmark)
        {
            return back()->with([
                'type' => 'danger',
                'message' => 'Admin yet to add benchmark, try again later.'
            ]); 
        }

        $resBench = [
            'amount' => $benchmark->amount,
            'benchmark_value' => $benchmark->benchmark_value,
        ];

        $benchMark = json_encode($resBench);
        
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $totalKG = $request->bags * rate + $request->bag_kg;
                
                    $sub = $benchmark->benchmark_value * $request->percentage;

                    $total = $sub * $totalKG;

                    $totalPrice = floor($total);
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $tinPayment = PaymentReceiptTin::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $totalKG,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'benchmark' => $benchMark,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $tinPayment->id,
                        'amount' => $tinPayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);
    
                    return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.tin.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.tin.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $request->kg;

                $totalPrice = floor($total);

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $tinPayment = PaymentReceiptTin::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'benchmark' => $benchMark,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $tinPayment->id,
                    'amount' => $tinPayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.tin.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.tin.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $totalKG = $request->bags * rate + $request->bag_kg;
                
                $sub = $benchmark->benchmark_value * $request->percentage;

                $total = $sub * $totalKG;

                $totalPrice = floor($total);

                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $sub = $benchmark->benchmark_value * $request->percentage;

            $total = $sub * $request->kg;

            $totalPrice = floor($total);

            return redirect()->route('payment.receipt.tin.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.tin.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $columbitePaymentReceiptPound = PaymentReceiptColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $columbitePaymentReceiptKg = PaymentReceiptColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_columbite_view', [
                    'columbitePaymentReceiptKg' => $columbitePaymentReceiptKg,
                    'columbitePaymentReceiptPound' => $columbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }
    }

    public function payment_receipt_columbite_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('assistant_managers.payment_receipt_columbite_add', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('assistant_managers.payment_receipt_columbite_add', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('assistant_managers.payment_receipt_columbite_add', compact('active_tab'));
        }
    }

    public function payment_receipt_columbite_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment = PaymentReceiptColumbite::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $columbitePayment->id,
                        'amount' => $columbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.columbite.view', 'pound'),
                        'message' => 'Payment Receipt created successfully'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }
    
                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $columbitePayment = PaymentReceiptColumbite::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $request->pounds,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $columbitePayment->id,
                    'amount' => $columbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.columbite.view', 'pound'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = floor($subTotal) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_columbite_kg_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('admin.payment.receipt.columbite.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $columbitePayment = PaymentReceiptColumbite::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $columbitePayment->id,
                        'amount' => $columbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;

                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $columbitePayment = PaymentReceiptColumbite::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $columbitePayment->id,
                    'amount' => $columbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.columbite.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_view($id, Request $request)
    {
        if($id == 'kg')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

        if($id == 'pound')
        {
            if($request->start_date == null && $request->end_date == null)
            {
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->get();
            } else {
                $lowergradecolumbitePaymentReceiptPound = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'pound', 'user_id' => Auth::user()->id])->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
                $lowergradecolumbitePaymentReceiptKg = PaymentReceiptLowerGradeColumbite::latest()->where(['type' => 'kg', 'user_id' => Auth::user()->id])->get();
            }

            $active_tab = $id;

            if($active_tab == 'pound') {
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } elseif($active_tab == 'kg') {
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            } else {
                $active_tab == 'kg';
                return view('assistant_managers.payment_receipt_lower_grade_columbite_view', [
                    'lowergradecolumbitePaymentReceiptKg' => $lowergradecolumbitePaymentReceiptKg,
                    'lowergradecolumbitePaymentReceiptPound' => $lowergradecolumbitePaymentReceiptPound,
                    'active_tab' => $active_tab,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]);
            }
        }

    }

    public function payment_receipt_lower_grade_columbite_add($id)
    {
        $active_tab = $id;

        if($active_tab == 'pound') {
            return view ('assistant_managers.payment_receipt_lower_grade_columbite_add', compact('active_tab'));
        } elseif($active_tab == 'kg') {
            return view ('assistant_managers.payment_receipt_lower_grade_columbite_add', compact('active_tab'));
        } else {
            $active_tab == 'kg';
            return view ('assistant_managers.payment_receipt_lower_grade_columbite_add', compact('active_tab'));
        }
    }

    public function payment_receipt_lower_grade_columbite_pound_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);
    
            if(!$berating)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('admin.payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_pounds < columbite_rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation;

                    $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);
            
                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $lgcolumbitePayment->id,
                        'amount' => $lgcolumbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.lower.grade.columbite.view', 'pound'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }
    
                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation;
    
                $total = floor($subTotal) * $request->pounds;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'pound' => $request->pounds,
                    'total_in_pound' => $request->pounds,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $lgcolumbitePayment->id,
                    'amount' => $lgcolumbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.lower.grade.columbite.view', 'pound'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_pounds < columbite_rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation;

                $subPrice = $request->bags * columbite_rate + $request->bag_pound;
                
                $total = $subTotal * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation;

            $total = $subTotal * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    public function payment_receipt_lower_grade_columbite_kg_post(Request $request)
    {
        if($request->save) 
        {
            $this->validate($request, [
                'supplier' => ['required', 'string', 'max:255'],
                'grade' => ['required', 'numeric'],
                'manager' => ['required', 'numeric'],
                'date_of_purchase' => ['required', 'date'],
                'receipt_no' => 'required|string',
                'receipt_image' => 'required|mimes:jpeg,png,jpg'
            ]);

            $berating = BeratingCalculation::find($request->grade);

            if(!$berating)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Admin yet to add this berating value, try again later.'
                ]); 
            }

            $response = [
                'grade' => $berating->grade,
                'price' => $berating->price,
                'unit_price' => $berating->unit_price
            ];

            $berate = json_encode($response);

            $manager = User::find($request->manager);

            if(!$manager)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                if($bag_kgs < rate)
                {
                    $per = $request->percentage / 100;

                    $rateCalculation = $dollarRate * $exchangeRate;

                    $subTotal = $per * $rateCalculation * fixed_rate;

                    $subPrice = $request->bags * rate + $request->bag_kg;
                    
                    $total = floor($subTotal) * $subPrice;

                    $totalPrice = number_format((float)$total, 0, '.', '');
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'kg' => $bag_kgs,
                        'total_in_kg' => $subPrice,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                        'date_of_purchase' => $request->date_of_purchase,
                        'receipt_no' => $request->receipt_no,
                        'receipt_image' => '/storage/payment_analysis/'.$filename
                    ]);

                    Transaction::create([
                        'user_id' => Auth::user()->id,
                        'accountant_process_id' => $lgcolumbitePayment->id,
                        'amount' => $lgcolumbitePayment->price,
                        'reference' => config('app.name'),
                        'status' => 'Payment Receipt'
                    ]);

                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'alertType' => 'success',
                        'back' => route('payment.receipt.lower.grade.columbite.view', 'kg'),
                        'message' => 'Payment Receipt created successfully.'
                    ]);
                } else {
                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
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
                        $myDollarRate = $analyses->dollar_rate;
                        $myExchangeRate = $analyses->exchange_rate;
                    }
                }

                $dollarRate = $myDollarRate ?? 0;
                $exchangeRate = $myExchangeRate ?? 0;

                if($dollarRate == 0 && $exchangeRate == 0)
                {
                    return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                        'type' => 'danger',
                        'message' => 'Percentage Analysis entered not found in our database, try again.'
                    ]);
                }

                $res = [
                    'dollar_rate' => $dollarRate,
                    'exchange_rate' => $exchangeRate,
                ];
    
                $analysisRate = json_encode($res);

                $per = $request->percentage / 100;
    
                $rateCalculation = $dollarRate * $exchangeRate;
    
                $subTotal = $per * $rateCalculation * fixed_rate;
    
                $total = floor($subTotal) * $request->kg;
    
                $totalPrice = number_format((float)$total, 0, '.', '');

                $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                    'type' => $request->type,
                    'user_id' => Auth::user()->id,
                    'supplier' => $request->supplier,
                    'staff' => $manager->id,
                    'grade' => $request->grade,
                    'kg' => $request->kg,
                    'total_in_kg' => $request->kg,
                    'berating_rate_list' => $berate,
                    'percentage_analysis' => $request->percentage,
                    'analysis_rate_list' => $analysisRate,
                    'price' => $this->replaceCharsInNumber($totalPrice, '0'),
                    'date_of_purchase' => $request->date_of_purchase,
                    'receipt_no' => $request->receipt_no,
                    'receipt_image' => '/storage/payment_analysis/'.$filename
                ]);
        
                Transaction::create([
                    'user_id' => Auth::user()->id,
                    'accountant_process_id' => $lgcolumbitePayment->id,
                    'amount' => $lgcolumbitePayment->price,
                    'reference' => config('app.name'),
                    'status' => 'Payment Receipt'
                ]);

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'alertType' => 'success',
                    'back' => route('payment.receipt.lower.grade.columbite.view', 'kg'),
                    'message' => 'Payment Receipt created successfully.'
                ]);
            } 

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'type' => 'danger',
                'message' => 'Please select weight type.'
            ]);
        }

        $this->validate($request, [
            'grade' => ['required', 'numeric'],
        ]);

        $berating = BeratingCalculation::find($request->grade);

        if(!$berating)
        {
            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            if($bag_kgs < rate)
            {
                $per = $request->percentage / 100;

                $rateCalculation = $dollarRate * $exchangeRate;

                $subTotal = $per * $rateCalculation * fixed_rate;

                $subPrice = $request->bags * rate + $request->bag_kg;
                
                $total = floor($subTotal) * $subPrice;

                $totalPrice = number_format((float)$total, 0, '.', '');

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => $this->replaceCharsInNumber($totalPrice, '0')
                ]);
            } else {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
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
                    $myDollarRate = $analyses->dollar_rate;
                    $myExchangeRate = $analyses->exchange_rate;
                }
            }

            $dollarRate = $myDollarRate ?? 0;
            $exchangeRate = $myExchangeRate ?? 0;

            if($dollarRate == 0 && $exchangeRate == 0)
            {
                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'type' => 'danger',
                    'message' => 'Percentage Analysis entered not found in our database, try again.'
                ]);
            }

            $per = $request->percentage / 100;

            $rateCalculation = $dollarRate * $exchangeRate;

            $subTotal = $per * $rateCalculation * fixed_rate;

            $total = floor($subTotal) * $request->kg;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => $this->replaceCharsInNumber($totalPrice, '0')
            ]);
        } 

        return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }
}
