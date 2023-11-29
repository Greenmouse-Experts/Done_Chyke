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
use Illuminate\Support\Facades\Crypt;

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

                    // $totalPrice = number_format((float)$total, 0, '.', '');
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
                        'pound' => $bag_pounds,
                        'total_in_pound' => $total_in_pounds,
                        'berating_rate_list' => $berate,
                        'price' => floor($totalPrice / 5) * 5,
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

                // $totalPrice = number_format((float)$total, 0, '.', '');
                $totalPrice = floor($total);

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
                    'price' => floor($totalPrice / 5) * 5,
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

                // $totalPrice = number_format((float)$total, 0, '.', '');
                $totalPrice = floor($total);

                return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => floor($totalPrice / 5) * 5
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

            // $totalPrice = number_format((float)$total, 0, '.', '');
            $totalPrice = floor($total);

            return redirect()->route('payment.receipt.tin.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => floor($totalPrice / 5) * 5
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
                        'price' => floor($totalPrice / 5) * 5,
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
                    'price' => floor($totalPrice / 5) * 5,
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
                    'message' => floor($totalPrice / 5) * 5
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
                'message' => floor($totalPrice / 5) * 5
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
                    $rateCalculation = $dollarRate * $exchangeRate * ($request->percentage / 100);

                    $subTotal = $rateCalculation * columbite_rate;

                    $subPrice = ($request->bags * columbite_rate + $request->bag_pound) * $subTotal;

                    $totalInPound = ($request->bags * columbite_rate + $request->bag_pound);
                    
                    $totalPrice = $subPrice / columbite_rate;
                    
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
                        'total_in_pound' => $totalInPound,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => floor($totalPrice / 5) * 5,
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
    
                $total = (floor($subTotal / 5) * 5) * $request->pounds;
    
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
                    'price' => floor($totalPrice / 5) * 5,
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
                $rateCalculation = $dollarRate * $exchangeRate * ($request->percentage / 100);

                $subTotal = $rateCalculation * columbite_rate;

                $subPrice = ($request->bags * columbite_rate + $request->bag_pound) * $subTotal;
                
                $totalPrice = $subPrice / columbite_rate;

                return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => floor($totalPrice / 5) * 5
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

            $total = (floor($subTotal / 5) * 5) * $request->pounds;

            $totalPrice = number_format((float)$total, 0, '.', '');

            return redirect()->route('payment.receipt.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => floor($totalPrice / 5) * 5
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
                        'price' => floor($totalPrice / 5) * 5,
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
                    'price' => floor($totalPrice / 5) * 5,
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
                    'message' => floor($totalPrice / 5) * 5
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
                'message' => floor($totalPrice / 5) * 5
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
                    $rateCalculation = $dollarRate * $exchangeRate * $per;
                    $pounds_weight = floor($rateCalculation / 5) * 5;
                    $subTotal = $rateCalculation * columbite_rate;
                    $subPrice = ($request->bags * columbite_rate + $request->bag_pound) * floor($subTotal / 5) * 5;
                    $total = $subPrice / columbite_rate;
                    $totalPrice = floor($total);
                    
                    $filename = uniqid(5).'-'.request()->receipt_image->getClientOriginalName();
                    request()->receipt_image->storeAs('payment_analysis', $filename, 'public');

                    $lgcolumbitePayment = PaymentReceiptLowerGradeColumbite::create([
                        'type' => $request->type,
                        'user_id' => Auth::user()->id,
                        'supplier' => $request->supplier,
                        'staff' => $manager->id,
                        'grade' => $request->grade,
                        'bag' => $request->bags,
                        'pound' => $bag_pounds,
                        'total_in_pound' => ($request->bags * columbite_rate + $request->bag_pound),
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => floor($totalPrice / 5) * 5,
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
                $rateCalculation = $dollarRate * $exchangeRate * $per;
                $subTotal = $rateCalculation * columbite_rate;
                $pounds_weight = floor($rateCalculation / 5) * 5;
                $total = $request->pounds * $pounds_weight;
                $totalPrice = floor($total);

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
                    'price' => floor($totalPrice / 5) * 5,
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
                $rateCalculation = $dollarRate * $exchangeRate * $per;
                $pounds_weight = floor($rateCalculation / 5) * 5;
                $subTotal = $rateCalculation * columbite_rate;
                $subPrice = ($request->bags * columbite_rate + $request->bag_pound) * floor($subTotal / 5) * 5;
                $total = $subPrice / columbite_rate;
                $totalPrice = floor($total);

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                    'previewPrice' => 'success',
                    'message' => floor($totalPrice / 5) * 5
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
            $rateCalculation = $dollarRate * $exchangeRate * $per;
            $subTotal = $rateCalculation * columbite_rate;
            $pounds_weight = floor($rateCalculation / 5) * 5;
            $total = $request->pounds * $pounds_weight;
            $totalPrice = floor($total);

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'pound')->with([
                'previewPrice' => 'success',
                'message' => floor($totalPrice / 5) * 5
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
                    $rateCalculation = $dollarRate * $exchangeRate * $per * fixed_rate;
                    $subTotal = floor($rateCalculation);
                    $subPrice = ($request->bags * rate + $request->bag_kg) * $subTotal;
                    $totalPrice = number_format((float)$subPrice, 0, '.', '');
                    
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
                        'total_in_kg' => $request->bags * rate + $request->bag_kg,
                        'berating_rate_list' => $berate,
                        'percentage_analysis' => $request->percentage,
                        'analysis_rate_list' => $analysisRate,
                        'price' => floor($totalPrice / 5) * 5,
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
                $rateCalculation = $dollarRate * $exchangeRate * $per * fixed_rate;
                $subTotal = floor($rateCalculation);
                $subPrice = $request->kg * $subTotal;
                $totalPrice = number_format((float)$subPrice, 0, '.', '');

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
                    'price' => floor($totalPrice / 5) * 5,
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
                $rateCalculation = $dollarRate * $exchangeRate * $per * fixed_rate;
                $subTotal = floor($rateCalculation);
                $subPrice = ($request->bags * rate + $request->bag_kg) * $subTotal;
                $totalPrice = number_format((float)$subPrice, 0, '.', '');

                return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                    'previewPrice' => 'success',
                    'message' => floor($totalPrice / 5) * 5
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
            $rateCalculation = $dollarRate * $exchangeRate * $per * fixed_rate;
            $subTotal = floor($rateCalculation);
            $subPrice = $request->kg * $subTotal;
            $totalPrice = number_format((float)$subPrice, 0, '.', '');

            return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
                'previewPrice' => 'success',
                'message' => floor($totalPrice / 5) * 5
            ]);
        } 

        return redirect()->route('payment.receipt.lower.grade.columbite.add', 'kg')->with([
            'type' => 'danger',
            'message' => 'Please select weight type.'
        ]);
    }

    // Weekly Material Summary
    public function weekly_material_summary_tin_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        } else {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'payment_receipt_tins.price', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'payment_receipt_tins.price', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->where('payment_receipt_tins.staff', $request->manager)
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'payment_receipt_tins.price', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } else {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'pound')  
                                ->where('payment_receipt_tins.staff', $request->manager)->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_pound', 'payment_receipt_tins.price', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        }

        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;
            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pound');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pound');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pound');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pound');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pound');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pound');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pound');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pound');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pound');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pound');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pound');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pound');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pound');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pound');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pound');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pound');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pound');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pound');

            $totalPound = $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5;

            $totalBerating =  $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205;

            $beratingAverage = $totalBerating / $totalPound;

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            $bags = $totalPound / 70;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 70;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193;
            $bag20 = $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 70;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 70;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 70;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 70;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 70;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 70;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $totalAmountPayable = $result->sum('price');
            $totalQualityInPounds = $result->sum('total_in_pound');

            $avgPrice = $totalAmountPayable / $totalQualityInPounds;
            $averagePrice = floor($avgPrice) * 70;
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.tin_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_material_summary_tin_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $tinPayment = PaymentReceiptTin::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($tinPayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($tinPayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.price', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.price', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->where('payment_receipt_tins.staff', $request->manager)
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.price', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        } else {
            $result =  PaymentReceiptTin::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_tins.grade')->latest()->where('payment_receipt_tins.type', 'kg')  
                                ->where('payment_receipt_tins.staff', $request->manager)->whereBetween('payment_receipt_tins.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_tins.date_of_purchase', 'payment_receipt_tins.total_in_kg', 'payment_receipt_tins.price', 'payment_receipt_tins.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_tins.created_at', 'payment_receipt_tins.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'kgs' => 0
            ];

            $totalBeratingAverage = 0;
            $totalPercentageAverage = 0;

            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            // Total Kg and Total Average Berating
            $sum188 = $result->where('grade', '18.8')->sum('total_in_kg');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_kg');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_kg');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_kg');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_kg');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_kg');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_kg');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_kg');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_kg');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_kg');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_kg');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_kg');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_kg');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_kg');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_kg');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_kg');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_kg');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_kg');

            $totalKg = $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5;

            $totalBerating =  $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205;

            $beratingAverage = $totalBerating / $totalKg;

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_kg;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_kg;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_kg;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_kg;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_kg;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_kg;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_kg;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_kg;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_kg;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_kg;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_kg;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_kg;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_kg;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_kg;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_kg;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_kg;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_kg;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_kg;
                }
            }

            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);

            $totalPercentage = $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205;

            $percentageAverage = $totalPercentage / $totalKg;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            $bags = $totalKg / 50;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 50;
            $totalBags = [
                'bags' => $str_arr[0],
                'kgs' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193;
            $bag20 = $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 50;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 50;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'kgs' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 50;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 50;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'kgs' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 50;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 50;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'kgs' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'kgs' => 0
                ];
            }
            
            $totalAmountPayable = $result->sum('price');
            $totalQualityInKg = $result->sum('total_in_kg');

            $avgPrice = $totalAmountPayable / $totalQualityInKg;
            $averagePrice = number_format((float)$avgPrice, 2, '.', '');
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.tin_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_material_summary_columbite_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        }else {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->where('payment_receipt_columbites.staff', $request->manager)
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'pound')  
                                ->where('payment_receipt_columbites.staff', $request->manager)->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_pound', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;
            $totalPercentageAverage = 0;
            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            // Total Kg and Total Average Berating
            $sum185 = $result->where('grade', '18.5')->sum('total_in_pound');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_pound');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_pound');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_pound');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_pound');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_pound');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_pound');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_pound');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_pound');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_pound');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_pound');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_pound');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_pound');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_pound');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_pound');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_pound');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_pound');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_pound');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_pound');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_pound');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_pound');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_pound');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_pound');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_pound');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_pound');

            $totalPound = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalPound; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_pound;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_pound;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_pound;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_pound;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_pound;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_pound;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_pound;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_pound;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_pound;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_pound;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_pound;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_pound;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_pound;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_pound;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_pound;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_pound;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_pound;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_pound;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_pound;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_pound;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_pound;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_pound;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_pound;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_pound;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_pound;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            $percentageAverage = $totalPercentage / $totalPound;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalPound / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 80;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 80;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 80;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 80;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 80;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 80;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $totalAmountPayable = $result->sum('price');
            $totalQualityInPounds = $result->sum('total_in_pound');

            $avgPrice = $totalAmountPayable / $totalQualityInPounds;
            $averagePrice = floor($avgPrice) * 80;
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.columbite_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_material_summary_columbite_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $columbitePayment = PaymentReceiptColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $colpayment)
            {
                $beratingpayment = BeratingCalculation::find($colpayment->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $colpayment->date_of_purchase, 'berating' => $berating->grade, 'total' => $colpayment->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_columbites.grade')->latest()->where('payment_receipt_columbites.type', 'kg')  
                                ->where('payment_receipt_columbites.staff', $request->manager)->whereBetween('payment_receipt_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_columbites.date_of_purchase', 'payment_receipt_columbites.total_in_kg', 'payment_receipt_columbites.price', 'payment_receipt_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_columbites.created_at', 'payment_receipt_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags18 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags19 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBags20 = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;
            $totalPercentageAverage = 0;
            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            // Total Kg and Total Average Berating
            $sum185 = $result->where('grade', '18.5')->sum('total_in_kg');
            $sum186 = $result->where('grade', '18.6')->sum('total_in_kg');
            $sum187 = $result->where('grade', '18.7')->sum('total_in_kg');
            $sum188 = $result->where('grade', '18.8')->sum('total_in_kg');
            $sum189 = $result->where('grade', '18.9')->sum('total_in_kg');
            $sum190 = $result->where('grade', '19.0')->sum('total_in_kg');
            $sum191 = $result->where('grade', '19.1')->sum('total_in_kg');
            $sum192 = $result->where('grade', '19.2')->sum('total_in_kg');
            $sum193 = $result->where('grade', '19.3')->sum('total_in_kg');
            $sum194 = $result->where('grade', '19.4')->sum('total_in_kg');
            $sum195 = $result->where('grade', '19.5')->sum('total_in_kg');
            $sum196 = $result->where('grade', '19.6')->sum('total_in_kg');
            $sum197 = $result->where('grade', '19.7')->sum('total_in_kg');
            $sum198 = $result->where('grade', '19.8')->sum('total_in_kg');
            $sum199 = $result->where('grade', '19.9')->sum('total_in_kg');
            $sum200 = $result->where('grade', '20.0')->sum('total_in_kg');
            $sum201 = $result->where('grade', '20.1')->sum('total_in_kg');
            $sum202 = $result->where('grade', '20.2')->sum('total_in_kg');
            $sum203 = $result->where('grade', '20.3')->sum('total_in_kg');
            $sum204 = $result->where('grade', '20.4')->sum('total_in_kg');
            $sum205 = $result->where('grade', '20.5')->sum('total_in_kg');
            $sum206 = $result->where('grade', '20.6')->sum('total_in_kg');
            $sum207 = $result->where('grade', '20.7')->sum('total_in_kg');
            $sum208 = $result->where('grade', '20.8')->sum('total_in_kg');
            $sum209 = $result->where('grade', '20.9')->sum('total_in_kg');

            $totalKg = $sum185 + $sum186 + $sum187 + $sum188 + $sum189 + $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199 + $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            $b185 = $sum185 * 18.5; $b186 = $sum186 * 18.6; $b187 = $sum187 * 18.7; $b188 = $sum188 * 18.8; $b189 = $sum189 * 18.9; $b190 = $sum190 * 19.0; $b191 = $sum191 * 19.1; $b192 = $sum192 * 19.2; $b193 = $sum193 * 19.3; $b194 = $sum194 * 19.4; $b195 = $sum195 * 19.5;  
            $b196 = $sum196 * 19.6; $b197 = $sum197 * 19.7;  $b198 = $sum198 * 19.8; $b199 = $sum199 * 19.9; $b200 = $sum200 * 20.0; $b201 = $sum201 * 20.1; $b202 = $sum202 * 20.2; $b203 = $sum203 * 20.3; 
            $b204 = $sum204 * 20.4; $b205 = $sum205 * 20.5; $b206 = $sum206 * 20.6; $b207 = $sum207 * 20.7; $b208 = $sum208 * 20.8; $b209 = $sum209 * 20.9;

            $totalBerating = $b185 + $b186 + $b187 + $b188 + $b189 + $b190 + $b191 + $b192 + $b193 + $b194 + $b195 + $b196 + $b197 + $b198 + $b199 + $b200 + $b201 +  $b202 + $b203 + $b204 + $b205 + $b206 + $b207 + $b208 + $b209;

            $beratingAverage = $totalBerating / $totalKg; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage185 = $result->where('grade', '18.5');
            if($percentage185->isEmpty())
            {
                $sumPercentage185[] = 0;
            } else {
                foreach($percentage185 as $per185)
                {
                    $sumPercentage185[] = $per185->percentage_analysis * $per185->total_in_kg;
                }
            }
            $percentage186 = $result->where('grade', '18.6');
            if($percentage186->isEmpty())
            {
                $sumPercentage186[] = 0;
            } else {
                foreach($percentage186 as $per186)
                {
                    $sumPercentage186[] = $per186->percentage_analysis * $per186->total_in_kg;
                }
            }
            $percentage187 = $result->where('grade', '18.7');
            if($percentage187->isEmpty())
            {
                $sumPercentage187[] = 0;
            } else {
                foreach($percentage187 as $per187)
                {
                    $sumPercentage187[] = $per187->percentage_analysis * $per187->total_in_kg;
                }
            }
            $percentage188 = $result->where('grade', '18.8');
            if($percentage188->isEmpty())
            {
                $sumPercentage188[] = 0;
            } else {
                foreach($percentage188 as $per188)
                {
                    $sumPercentage188[] = $per188->percentage_analysis * $per188->total_in_kg;
                }
            }
            $percentage189 = $result->where('grade', '18.9');
            if($percentage189->isEmpty())
            {
                $sumPercentage189[] = 0;
            } else {
                foreach($percentage189 as $per189)
                {
                    $sumPercentage189[] = $per189->percentage_analysis * $per189->total_in_kg;
                }
            }
            $percentage190 = $result->where('grade', '19.0');
            if($percentage190->isEmpty())
            {
                $sumPercentage190[] = 0;
            } else {
                foreach($percentage190 as $per190)
                {
                    $sumPercentage190[] = $per190->percentage_analysis * $per190->total_in_kg;
                }
            }
            $percentage191 = $result->where('grade', '19.1');
            if($percentage191->isEmpty())
            {
                $sumPercentage191[] = 0;
            } else {
                foreach($percentage191 as $per191)
                {
                    $sumPercentage191[] = $per191->percentage_analysis * $per191->total_in_kg;
                }
            }
            $percentage192 = $result->where('grade', '19.2');
            if($percentage192->isEmpty())
            {
                $sumPercentage192[] = 0;
            } else {
                foreach($percentage192 as $per192)
                {
                    $sumPercentage192[] = $per192->percentage_analysis * $per192->total_in_kg;
                }
            }
            $percentage193 = $result->where('grade', '19.3');
            if($percentage193->isEmpty())
            {
                $sumPercentage193[] = 0;
            } else {
                foreach($percentage193 as $per193)
                {
                    $sumPercentage193[] = $per193->percentage_analysis * $per193->total_in_kg;
                }
            }
            $percentage194 = $result->where('grade', '19.4');
            if($percentage194->isEmpty())
            {
                $sumPercentage194[] = 0;
            } else {
                foreach($percentage194 as $per194)
                {
                    $sumPercentage194[] = $per194->percentage_analysis * $per194->total_in_kg;
                }
            }
            $percentage195 = $result->where('grade', '19.5');
            if($percentage195->isEmpty())
            {
                $sumPercentage195[] = 0;
            } else {
                foreach($percentage195 as $per195)
                {
                    $sumPercentage195[] = $per195->percentage_analysis * $per195->total_in_kg;
                }
            }
            $percentage196 = $result->where('grade', '19.6');
            if($percentage196->isEmpty())
            {
                $sumPercentage196[] = 0;
            } else {
                foreach($percentage196 as $per196)
                {
                    $sumPercentage196[] = $per196->percentage_analysis * $per196->total_in_kg;
                }
            }
            $percentage197 = $result->where('grade', '19.7');
            if($percentage197->isEmpty())
            {
                $sumPercentage197[] = 0;
            } else {
                foreach($percentage197 as $per197)
                {
                    $sumPercentage197[] = $per197->percentage_analysis * $per197->total_in_kg;
                }
            }
            $percentage198 = $result->where('grade', '19.8');
            if($percentage198->isEmpty())
            {
                $sumPercentage198[] = 0;
            } else {
                foreach($percentage198 as $per198)
                {
                    $sumPercentage198[] = $per198->percentage_analysis * $per198->total_in_kg;
                }
            }
            $percentage199 = $result->where('grade', '19.9');
            if($percentage199->isEmpty())
            {
                $sumPercentage199[] = 0;
            } else {
                foreach($percentage199 as $per199)
                {
                    $sumPercentage199[] = $per199->percentage_analysis * $per199->total_in_kg;
                }
            }
            $percentage200 = $result->where('grade', '20.0');
            if($percentage200->isEmpty())
            {
                $sumPercentage200[] = 0;
            } else {
                foreach($percentage200 as $per200)
                {
                    $sumPercentage200[] = $per200->percentage_analysis * $per200->total_in_kg;
                }
            }
            $percentage201 = $result->where('grade', '20.1');
            if($percentage201->isEmpty())
            {
                $sumPercentage201[] = 0;
            } else {
                foreach($percentage201 as $per201)
                {
                    $sumPercentage201[] = $per201->percentage_analysis * $per201->total_in_kg;
                }
            }
            $percentage202 = $result->where('grade', '20.2');
            if($percentage202->isEmpty())
            {
                $sumPercentage202[] = 0;
            } else {
                foreach($percentage202 as $per202)
                {
                    $sumPercentage202[] = $per202->percentage_analysis * $per202->total_in_kg;
                }
            }
            $percentage203 = $result->where('grade', '20.3');
            if($percentage203->isEmpty())
            {
                $sumPercentage203[] = 0;
            } else {
                foreach($percentage203 as $per203)
                {
                    $sumPercentage203[] = $per203->percentage_analysis * $per203->total_in_kg;
                }
            }
            $percentage204 = $result->where('grade', '20.4');
            if($percentage204->isEmpty())
            {
                $sumPercentage204[] = 0;
            } else {
                foreach($percentage204 as $per204)
                {
                    $sumPercentage204[] = $per204->percentage_analysis * $per204->total_in_kg;
                }
            }
            $percentage205 = $result->where('grade', '20.5');
            if($percentage205->isEmpty())
            {
                $sumPercentage205[] = 0;
            } else {
                foreach($percentage205 as $per205)
                {
                    $sumPercentage205[] = $per205->percentage_analysis * $per205->total_in_kg;
                }
            }
            $percentage206 = $result->where('grade', '20.6');
            if($percentage206->isEmpty())
            {
                $sumPercentage206[] = 0;
            } else {
                foreach($percentage206 as $per206)
                {
                    $sumPercentage206[] = $per206->percentage_analysis * $per206->total_in_kg;
                }
            }
            $percentage207 = $result->where('grade', '20.7');
            if($percentage207->isEmpty())
            {
                $sumPercentage207[] = 0;
            } else {
                foreach($percentage207 as $per207)
                {
                    $sumPercentage207[] = $per207->percentage_analysis * $per207->total_in_kg;
                }
            }
            $percentage208 = $result->where('grade', '20.8');
            if($percentage208->isEmpty())
            {
                $sumPercentage208[] = 0;
            } else {
                foreach($percentage208 as $per208)
                {
                    $sumPercentage208[] = $per208->percentage_analysis * $per208->total_in_kg;
                }
            }
            $percentage209 = $result->where('grade', '20.9');
            if($percentage209->isEmpty())
            {
                $sumPercentage209[] = 0;
            } else {
                foreach($percentage209 as $per209)
                {
                    $sumPercentage209[] = $per209->percentage_analysis * $per209->total_in_kg;
                }
            }

            $totalPercentage185 = array_sum($sumPercentage185); 
            $totalPercentage186 = array_sum($sumPercentage186); 
            $totalPercentage187 = array_sum($sumPercentage187); 
            $totalPercentage188 = array_sum($sumPercentage188); 
            $totalPercentage189 = array_sum($sumPercentage189); 
            $totalPercentage190 = array_sum($sumPercentage190);
            $totalPercentage191 = array_sum($sumPercentage191);
            $totalPercentage192 = array_sum($sumPercentage192);
            $totalPercentage193 = array_sum($sumPercentage193);
            $totalPercentage194 = array_sum($sumPercentage194); 
            $totalPercentage195 = array_sum($sumPercentage195); 
            $totalPercentage196 = array_sum($sumPercentage196);
            $totalPercentage197 = array_sum($sumPercentage197);
            $totalPercentage198 = array_sum($sumPercentage198); 
            $totalPercentage199 = array_sum($sumPercentage199);
            $totalPercentage200 = array_sum($sumPercentage200); 
            $totalPercentage201 = array_sum($sumPercentage201);
            $totalPercentage202 = array_sum($sumPercentage202);
            $totalPercentage203 = array_sum($sumPercentage203);
            $totalPercentage204 = array_sum($sumPercentage204); 
            $totalPercentage205 = array_sum($sumPercentage205);
            $totalPercentage206 = array_sum($sumPercentage206);
            $totalPercentage207 = array_sum($sumPercentage207);
            $totalPercentage208 = array_sum($sumPercentage208); 
            $totalPercentage209 = array_sum($sumPercentage209);

            $totalPercentage = $totalPercentage185 + $totalPercentage186 + $totalPercentage187 + $totalPercentage188 + $totalPercentage189 + $totalPercentage190 + $totalPercentage191 + $totalPercentage192 + $totalPercentage193 + $totalPercentage194 + $totalPercentage195 + $totalPercentage196 + $totalPercentage197 + $totalPercentage198 + $totalPercentage199 + $totalPercentage200 + $totalPercentage201 + $totalPercentage202 + $totalPercentage203 + $totalPercentage204 + $totalPercentage205 + $totalPercentage206 + $totalPercentage207 + $totalPercentage208 + $totalPercentage209;

            $percentageAverage = $totalPercentage / $totalKg;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalKg / 50;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 50;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $bag18 = $sum185 + $sum186 + $sum187 + $sum188 + $sum189;
            $bag19 = $sum190 + $sum191 + $sum192 + $sum193 + $sum194 + $sum195 + $sum196 + $sum197 + $sum198 + $sum199;
            $bag20 = $sum200 + $sum201 + $sum202 + $sum203 + $sum204 + $sum205 + $sum206 + $sum207 + $sum208 + $sum209;

            if($bag18 > 0)
            {
                $bag18Bags = $bag18 / 50;
                $str_arr18 = explode('.',$bag18Bags);
                $str18 = str_replace($str_arr18[0], '0.', $str_arr18[0]);
                $strPound = $str_arr18[1] ?? 0;
                $substr18 = $str18.''.$strPound;
                $answer18 = $substr18 * 50;
                $totalBags18 = [
                    'bags' => $str_arr18[0],
                    'pounds' => number_format((float)$answer18, 0, '.', '')
                ];
            } else {
                $totalBags18 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag19 > 0)
            {
                $bag19Bags = $bag19 / 50;
                $str_arr19 = explode('.',$bag19Bags);
                $str19 = str_replace($str_arr19[0], '0.', $str_arr19[0]);
                $strPound = $str_arr19[1] ?? 0;
                $substr19 = $str19.''.$strPound;
                $answer19 = $substr19 * 50;
                $totalBags19 = [
                    'bags' => $str_arr19[0],
                    'pounds' => number_format((float)$answer19, 0, '.', '')
                ];
            } else {
                $totalBags19 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }

            if($bag20 > 0)
            {
                $bag20Bags = $bag20 / 50;
                $str_arr20 = explode('.',$bag20Bags);
                $str20 = str_replace($str_arr20[0], '0.', $str_arr20[0]);
                $strPound = $str_arr20[1] ?? 0;
                $substr20 = $str20.''.$strPound;
                $answer20 = $substr20 * 50;
                $totalBags20 = [
                    'bags' => $str_arr20[0],
                    'pounds' => number_format((float)$answer20, 0, '.', '')
                ];
            } else {
                $totalBags20 = [
                    'bags' => 0,
                    'pounds' => 0
                ];
            }
            
            $totalAmountPayable = $result->sum('price');
            $totalQualityInKg = $result->sum('total_in_kg');

            $avgPrice = $totalAmountPayable / $totalQualityInKg;
            $averagePrice = number_format((float)$avgPrice, 2, '.', '');
            
            $data = ['18M' => $totalBags18, '19M' => $totalBags19, '20M' => $totalBags20, 'TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.columbite_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }  

    public function weekly_material_summary_low_grade_pound(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->get();
        }else {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'pound')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $tinpound)
            {
                $beratingpayment = BeratingCalculation::find($tinpound->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $tinpound->date_of_purchase, 'berating' => $berating->grade, 'total' => $tinpound->total_in_pound];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'pound')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_pound', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        }

        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;
            $totalPercentageAverage = 0;
            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            // Total Kg and Total Average Berating
            $sum210 = $result->where('grade', '21.0')->sum('total_in_pound');
            $sum211 = $result->where('grade', '21.1')->sum('total_in_pound');
            $sum212 = $result->where('grade', '21.2')->sum('total_in_pound');
            $sum213 = $result->where('grade', '21.3')->sum('total_in_pound');
            $sum214 = $result->where('grade', '21.4')->sum('total_in_pound');
            $sum215 = $result->where('grade', '21.5')->sum('total_in_pound');
            $sum216 = $result->where('grade', '21.6')->sum('total_in_pound');
            $sum217 = $result->where('grade', '21.7')->sum('total_in_pound');
            $sum218 = $result->where('grade', '21.8')->sum('total_in_pound');
            $sum219 = $result->where('grade', '21.9')->sum('total_in_pound');
            $sum220 = $result->where('grade', '22.0')->sum('total_in_pound');
            $sum221 = $result->where('grade', '22.1')->sum('total_in_pound');
            $sum222 = $result->where('grade', '22.2')->sum('total_in_pound');
            $sum223 = $result->where('grade', '22.3')->sum('total_in_pound');
            $sum224 = $result->where('grade', '22.4')->sum('total_in_pound');
            $sum225 = $result->where('grade', '22.5')->sum('total_in_pound');
            $sum226 = $result->where('grade', '22.6')->sum('total_in_pound');
            $sum227 = $result->where('grade', '22.7')->sum('total_in_pound');
            $sum228 = $result->where('grade', '22.8')->sum('total_in_pound');
            $sum229 = $result->where('grade', '22.9')->sum('total_in_pound');
            $sum230 = $result->where('grade', '23.0')->sum('total_in_pound');
            $sum231 = $result->where('grade', '23.1')->sum('total_in_pound');
            $sum232 = $result->where('grade', '23.2')->sum('total_in_pound');
            $sum233 = $result->where('grade', '23.3')->sum('total_in_pound');
            $sum234 = $result->where('grade', '23.4')->sum('total_in_pound');
            $sum235 = $result->where('grade', '23.5')->sum('total_in_pound');

            $totalPound =  $sum210 + $sum211 + $sum212 + $sum213 + $sum214 + $sum215 + $sum216 + $sum217 + $sum218 + $sum219 + $sum220 + $sum221 + $sum222 + $sum223 + $sum224 + $sum225 + $sum226 + $sum227 + $sum228 + $sum229 + $sum230 + $sum231 + $sum232 + $sum233 + $sum234 + $sum235;

            $b210 = $sum210 * 21.0; $b211 = $sum211 * 21.1; $b212 = $sum212 * 21.2; $b213 = $sum213 * 21.3; $b214 = $sum214 * 21.4; $b215 = $sum215 * 21.5; $b216 = $sum216 * 21.6; $b217 = $sum217 * 21.7; $b218 = $sum218 * 21.8; $b219 = $sum219 * 21.9; $b220 = $sum220 * 22.0;  
            $b221 = $sum221 * 22.1; $b222 = $sum222 * 22.2;  $b223 = $sum223 * 22.3; $b224 = $sum224 * 22.4; $b225 = $sum225 * 22.5; $b226 = $sum226 * 22.6; $b227 = $sum227 * 22.7; $b228 = $sum228 * 22.8; 
            $b229 = $sum229 * 22.9; $b230 = $sum230 * 23.0; $b231 = $sum231 * 23.1; $b232 = $sum232 * 23.2; $b233 = $sum233 * 23.3; $b234 = $sum234 * 23.4 + $b235 = $sum235 * 23.5;

            $totalBerating = $b210 + $b211 + $b212 + $b213 + $b214 + $b215 + $b216 + $b217 + $b218 + $b219 + $b220 + $b221 + $b222 + $b223 + $b224 + $b225 + $b226 + $b227 + $b228 + $b229 + $b230 + $b231 + $b232 + $b233 + $b234 + $b235;

            $beratingAverage = $totalBerating / $totalPound; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage210 = $result->where('grade', '21.0');
            if($percentage210->isEmpty())
            {
                $sumPercentage210[] = 0;
            } else {
                foreach($percentage210 as $per210)
                {
                    $sumPercentage210[] = $per210->percentage_analysis * $per210->total_in_pound;
                }
            }
            $percentage211 = $result->where('grade', '21.1');
            if($percentage211->isEmpty())
            {
                $sumPercentage211[] = 0;
            } else {
                foreach($percentage211 as $per211)
                {
                    $sumPercentage211[] = $per211->percentage_analysis * $per211->total_in_pound;
                }
            }
            $percentage212 = $result->where('grade', '21.2');
            if($percentage212->isEmpty())
            {
                $sumPercentage212[] = 0;
            } else {
                foreach($percentage212 as $per212)
                {
                    $sumPercentage212[] = $per212->percentage_analysis * $per212->total_in_pound;
                }
            }
            $percentage213 = $result->where('grade', '21.3');
            if($percentage213->isEmpty())
            {
                $sumPercentage213[] = 0;
            } else {
                foreach($percentage213 as $per213)
                {
                    $sumPercentage213[] = $per213->percentage_analysis * $per213->total_in_pound;
                }
            }
            $percentage214 = $result->where('grade', '21.4');
            if($percentage214->isEmpty())
            {
                $sumPercentage214[] = 0;
            } else {
                foreach($percentage214 as $per214)
                {
                    $sumPercentage214[] = $per214->percentage_analysis * $per214->total_in_pound;
                }
            }
            $percentage215 = $result->where('grade', '21.5');
            if($percentage215->isEmpty())
            {
                $sumPercentage215[] = 0;
            } else {
                foreach($percentage215 as $per215)
                {
                    $sumPercentage215[] = $per215->percentage_analysis * $per215->total_in_pound;
                }
            }
            $percentage216 = $result->where('grade', '21.6');
            if($percentage216->isEmpty())
            {
                $sumPercentage216[] = 0;
            } else {
                foreach($percentage216 as $per216)
                {
                    $sumPercentage216[] = $per216->percentage_analysis * $per216->total_in_pound;
                }
            }
            $percentage217 = $result->where('grade', '21.7');
            if($percentage217->isEmpty())
            {
                $sumPercentage217[] = 0;
            } else {
                foreach($percentage217 as $per217)
                {
                    $sumPercentage217[] = $per217->percentage_analysis * $per217->total_in_pound;
                }
            }
            $percentage218 = $result->where('grade', '21.8');
            if($percentage218->isEmpty())
            {
                $sumPercentage218[] = 0;
            } else {
                foreach($percentage218 as $per218)
                {
                    $sumPercentage218[] = $per218->percentage_analysis * $per218->total_in_pound;
                }
            }
            $percentage219 = $result->where('grade', '21.9');
            if($percentage219->isEmpty())
            {
                $sumPercentage219[] = 0;
            } else {
                foreach($percentage219 as $per219)
                {
                    $sumPercentage219[] = $per219->percentage_analysis * $per219->total_in_pound;
                }
            }
            $percentage220 = $result->where('grade', '22.0');
            if($percentage220->isEmpty())
            {
                $sumPercentage220[] = 0;
            } else {
                foreach($percentage220 as $per220)
                {
                    $sumPercentage220[] = $per220->percentage_analysis * $per220->total_in_pound;
                }
            }
            $percentage221 = $result->where('grade', '22.1');
            if($percentage221->isEmpty())
            {
                $sumPercentage221[] = 0;
            } else {
                foreach($percentage221 as $per221)
                {
                    $sumPercentage221[] = $per221->percentage_analysis * $per221->total_in_pound;
                }
            }
            $percentage222 = $result->where('grade', '22.2');
            if($percentage222->isEmpty())
            {
                $sumPercentage222[] = 0;
            } else {
                foreach($percentage222 as $per222)
                {
                    $sumPercentage222[] = $per222->percentage_analysis * $per222->total_in_pound;
                }
            }
            $percentage223 = $result->where('grade', '22.3');
            if($percentage223->isEmpty())
            {
                $sumPercentage223[] = 0;
            } else {
                foreach($percentage223 as $per223)
                {
                    $sumPercentage223[] = $per223->percentage_analysis * $per223->total_in_pound;
                }
            }
            $percentage224 = $result->where('grade', '22.4');
            if($percentage224->isEmpty())
            {
                $sumPercentage224[] = 0;
            } else {
                foreach($percentage224 as $per224)
                {
                    $sumPercentage224[] = $per224->percentage_analysis * $per224->total_in_pound;
                }
            }
            $percentage225 = $result->where('grade', '22.5');
            if($percentage225->isEmpty())
            {
                $sumPercentage225[] = 0;
            } else {
                foreach($percentage225 as $per225)
                {
                    $sumPercentage225[] = $per225->percentage_analysis * $per225->total_in_pound;
                }
            }
            $percentage226 = $result->where('grade', '22.6');
            if($percentage226->isEmpty())
            {
                $sumPercentage226[] = 0;
            } else {
                foreach($percentage226 as $per226)
                {
                    $sumPercentage226[] = $per226->percentage_analysis * $per226->total_in_pound;
                }
            }
            $percentage227 = $result->where('grade', '22.7');
            if($percentage227->isEmpty())
            {
                $sumPercentage227[] = 0;
            } else {
                foreach($percentage227 as $per227)
                {
                    $sumPercentage227[] = $per227->percentage_analysis * $per227->total_in_pound;
                }
            }
            $percentage228 = $result->where('grade', '22.8');
            if($percentage228->isEmpty())
            {
                $sumPercentage228[] = 0;
            } else {
                foreach($percentage228 as $per228)
                {
                    $sumPercentage228[] = $per228->percentage_analysis * $per228->total_in_pound;
                }
            }
            $percentage229 = $result->where('grade', '22.9');
            if($percentage229->isEmpty())
            {
                $sumPercentage229[] = 0;
            } else {
                foreach($percentage229 as $per229)
                {
                    $sumPercentage229[] = $per229->percentage_analysis * $per229->total_in_pound;
                }
            }
            $percentage230 = $result->where('grade', '23.0');
            if($percentage230->isEmpty())
            {
                $sumPercentage230[] = 0;
            } else {
                foreach($percentage230 as $per230)
                {
                    $sumPercentage230[] = $per230->percentage_analysis * $per230->total_in_pound;
                }
            }
            $percentage231 = $result->where('grade', '23.1');
            if($percentage231->isEmpty())
            {
                $sumPercentage231[] = 0;
            } else {
                foreach($percentage231 as $per231)
                {
                    $sumPercentage231[] = $per231->percentage_analysis * $per231->total_in_pound;
                }
            }
            $percentage232 = $result->where('grade', '23.2');
            if($percentage232->isEmpty())
            {
                $sumPercentage232[] = 0;
            } else {
                foreach($percentage232 as $per232)
                {
                    $sumPercentage232[] = $per232->percentage_analysis * $per232->total_in_pound;
                }
            }
            $percentage233 = $result->where('grade', '23.3');
            if($percentage233->isEmpty())
            {
                $sumPercentage233[] = 0;
            } else {
                foreach($percentage233 as $per233)
                {
                    $sumPercentage233[] = $per233->percentage_analysis * $per233->total_in_pound;
                }
            }
            $percentage234 = $result->where('grade', '23.4');
            if($percentage234->isEmpty())
            {
                $sumPercentage234[] = 0;
            } else {
                foreach($percentage234 as $per234)
                {
                    $sumPercentage234[] = $per234->percentage_analysis * $per234->total_in_pound;
                }
            }

            $percentage235 = $result->where('grade', '23.5');
            if($percentage235->isEmpty())
            {
                $sumPercentage235[] = 0;
            } else {
                foreach($percentage235 as $per235)
                {
                    $sumPercentage235[] = $per235->percentage_analysis * $per235->total_in_pound;
                }
            }

            $totalPercentage210 = array_sum($sumPercentage210); 
            $totalPercentage211 = array_sum($sumPercentage211); 
            $totalPercentage212 = array_sum($sumPercentage212); 
            $totalPercentage213 = array_sum($sumPercentage213); 
            $totalPercentage214 = array_sum($sumPercentage214); 
            $totalPercentage215 = array_sum($sumPercentage215);
            $totalPercentage216 = array_sum($sumPercentage216);
            $totalPercentage217 = array_sum($sumPercentage217);
            $totalPercentage218 = array_sum($sumPercentage218);
            $totalPercentage219 = array_sum($sumPercentage219); 
            $totalPercentage220 = array_sum($sumPercentage220); 
            $totalPercentage221 = array_sum($sumPercentage221);
            $totalPercentage222 = array_sum($sumPercentage222);
            $totalPercentage223 = array_sum($sumPercentage223); 
            $totalPercentage224 = array_sum($sumPercentage224);
            $totalPercentage225 = array_sum($sumPercentage225); 
            $totalPercentage226 = array_sum($sumPercentage226);
            $totalPercentage227 = array_sum($sumPercentage227);
            $totalPercentage228 = array_sum($sumPercentage228);
            $totalPercentage229 = array_sum($sumPercentage229); 
            $totalPercentage230 = array_sum($sumPercentage230);
            $totalPercentage231 = array_sum($sumPercentage231);
            $totalPercentage232 = array_sum($sumPercentage232);
            $totalPercentage233 = array_sum($sumPercentage233); 
            $totalPercentage234 = array_sum($sumPercentage234);
            $totalPercentage235 = array_sum($sumPercentage235);

            $totalPercentage = $totalPercentage210 + $totalPercentage211 + $totalPercentage212 + $totalPercentage213 + $totalPercentage214 + $totalPercentage215 + $totalPercentage216 + $totalPercentage217 + $totalPercentage218 + $totalPercentage219 +  $totalPercentage220 + $totalPercentage221 + $totalPercentage222 + $totalPercentage223 + $totalPercentage224 + $totalPercentage225 + $totalPercentage226 + $totalPercentage227 + $totalPercentage228 + $totalPercentage229 + $totalPercentage230 + $totalPercentage231 + $totalPercentage232 + $totalPercentage233 + $totalPercentage234 + $totalPercentage235;
            
            $percentageAverage = $totalPercentage / $totalPound;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            $bags = $totalPound / 80;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 80;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $totalAmountPayable = $result->sum('price');
            $totalQualityInPounds = $result->sum('total_in_pound');

            $avgPrice = $totalAmountPayable / $totalQualityInPounds;
            $averagePrice = floor($avgPrice) * 80;
  
            $data = ['TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.low_grade_pound', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }

    public function weekly_material_summary_low_grade_kg(Request $request)
    {
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->get();
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->get();
        } else {
            $columbitePayment = PaymentReceiptLowerGradeColumbite::latest()->where('type', 'kg')->where('staff', $request->manager)->whereBetween('date_of_purchase', [$request->start_date, $request->end_date])->get();
        }

        if($columbitePayment->isEmpty())
        {
            $analysis = [];

        } else {
            
            $beratingCalculation = BeratingCalculation::latest()->get();

            foreach($columbitePayment as $colpayment)
            {
                $beratingpayment = BeratingCalculation::find($colpayment->grade);

                foreach($beratingCalculation as $berating)
                {
                    if($berating->grade == $beratingpayment->grade)
                    {
                        $data[] = ['date' => $colpayment->date_of_purchase, 'berating' => $berating->grade, 'total' => $colpayment->total_in_kg];

                        $analysis = array_values(array_unique($data, 0));
                                    
                        rsort($analysis);
                    }
                }
            }
        }

        // Calculation Starts
        if($request->start_date == null && $request->end_date == null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'kg')  
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_kg', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date !== null && $request->end_date !== null && $request->manager == null)
        {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'kg')  
                                ->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_kg', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } elseif($request->start_date == null && $request->end_date == null && $request->manager !== null)
        { 
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'kg')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_kg', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        } else {
            $result =  PaymentReceiptLowerGradeColumbite::join('berating_calculations', 'berating_calculations.id', '=', 'payment_receipt_lower_grade_columbites.grade')->latest()->where('payment_receipt_lower_grade_columbites.type', 'kg')  
                                ->where('payment_receipt_lower_grade_columbites.staff', $request->manager)->whereBetween('payment_receipt_lower_grade_columbites.date_of_purchase', [$request->start_date, $request->end_date])
                                ->get(['payment_receipt_lower_grade_columbites.date_of_purchase', 'payment_receipt_lower_grade_columbites.total_in_kg', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.price', 'payment_receipt_lower_grade_columbites.percentage_analysis', 'berating_calculations.grade', 'payment_receipt_lower_grade_columbites.created_at', 'payment_receipt_lower_grade_columbites.updated_at']);
        }
        
        if($result->isEmpty())
        {
            $totalBags = [
                'bags' => 0,
                'pounds' => 0
            ];

            $totalBeratingAverage = 0;
            $totalPercentageAverage = 0;
            $totalAmountPayable = 0;
            $averagePrice = 0;

            $data = ['TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];

        } else {
            // Total Kg and Total Average Berating
            $sum210 = $result->where('grade', '21.0')->sum('total_in_kg');
            $sum211 = $result->where('grade', '21.1')->sum('total_in_kg');
            $sum212 = $result->where('grade', '21.2')->sum('total_in_kg');
            $sum213 = $result->where('grade', '21.3')->sum('total_in_kg');
            $sum214 = $result->where('grade', '21.4')->sum('total_in_kg');
            $sum215 = $result->where('grade', '21.5')->sum('total_in_kg');
            $sum216 = $result->where('grade', '21.6')->sum('total_in_kg');
            $sum217 = $result->where('grade', '21.7')->sum('total_in_kg');
            $sum218 = $result->where('grade', '21.8')->sum('total_in_kg');
            $sum219 = $result->where('grade', '21.9')->sum('total_in_kg');
            $sum220 = $result->where('grade', '22.0')->sum('total_in_kg');
            $sum221 = $result->where('grade', '22.1')->sum('total_in_kg');
            $sum222 = $result->where('grade', '22.2')->sum('total_in_kg');
            $sum223 = $result->where('grade', '22.3')->sum('total_in_kg');
            $sum224 = $result->where('grade', '22.4')->sum('total_in_kg');
            $sum225 = $result->where('grade', '22.5')->sum('total_in_kg');
            $sum226 = $result->where('grade', '22.6')->sum('total_in_kg');
            $sum227 = $result->where('grade', '22.7')->sum('total_in_kg');
            $sum228 = $result->where('grade', '22.8')->sum('total_in_kg');
            $sum229 = $result->where('grade', '22.9')->sum('total_in_kg');
            $sum230 = $result->where('grade', '23.0')->sum('total_in_kg');
            $sum231 = $result->where('grade', '23.1')->sum('total_in_kg');
            $sum232 = $result->where('grade', '23.2')->sum('total_in_kg');
            $sum233 = $result->where('grade', '23.3')->sum('total_in_kg');
            $sum234 = $result->where('grade', '23.4')->sum('total_in_kg');
            $sum235 = $result->where('grade', '23.5')->sum('total_in_kg');

            $totalKG =  $sum210 + $sum211 + $sum212 + $sum213 + $sum214 + $sum215 + $sum216 + $sum217 + $sum218 + $sum219 + $sum220 + $sum221 + $sum222 + $sum223 + $sum224 + $sum225 + $sum226 + $sum227 + $sum228 + $sum229 + $sum230 + $sum231 + $sum232 + $sum233 + $sum234 + $sum235;

            $b210 = $sum210 * 21.0; $b211 = $sum211 * 21.1; $b212 = $sum212 * 21.2; $b213 = $sum213 * 21.3; $b214 = $sum214 * 21.4; $b215 = $sum215 * 21.5; $b216 = $sum216 * 21.6; $b217 = $sum217 * 21.7; $b218 = $sum218 * 21.8; $b219 = $sum219 * 21.9; $b220 = $sum220 * 22.0;  
            $b221 = $sum221 * 22.1; $b222 = $sum222 * 22.2;  $b223 = $sum223 * 22.3; $b224 = $sum224 * 22.4; $b225 = $sum225 * 22.5; $b226 = $sum226 * 22.6; $b227 = $sum227 * 22.7; $b228 = $sum228 * 22.8; 
            $b229 = $sum229 * 22.9; $b230 = $sum230 * 23.0; $b231 = $sum231 * 23.1; $b232 = $sum232 * 23.2; $b233 = $sum233 * 23.3; $b234 = $sum234 * 23.4 + $b235 = $sum235 * 23.5;

            $totalBerating = $b210 + $b211 + $b212 + $b213 + $b214 + $b215 + $b216 + $b217 + $b218 + $b219 + $b220 + $b221 + $b222 + $b223 + $b224 + $b225 + $b226 + $b227 + $b228 + $b229 + $b230 + $b231 + $b232 + $b233 + $b234 + $b235;

            $beratingAverage = $totalBerating / $totalKG; 

            $totalBeratingAverage = number_format((float)$beratingAverage, 2, '.', '');

            // Percentage Average
            $percentage210 = $result->where('grade', '21.0');
            if($percentage210->isEmpty())
            {
                $sumPercentage210[] = 0;
            } else {
                foreach($percentage210 as $per210)
                {
                    $sumPercentage210[] = $per210->percentage_analysis * $per210->total_in_kg;
                }
            }
            $percentage211 = $result->where('grade', '21.1');
            if($percentage211->isEmpty())
            {
                $sumPercentage211[] = 0;
            } else {
                foreach($percentage211 as $per211)
                {
                    $sumPercentage211[] = $per211->percentage_analysis * $per211->total_in_kg;
                }
            }
            $percentage212 = $result->where('grade', '21.2');
            if($percentage212->isEmpty())
            {
                $sumPercentage212[] = 0;
            } else {
                foreach($percentage212 as $per212)
                {
                    $sumPercentage212[] = $per212->percentage_analysis * $per212->total_in_kg;
                }
            }
            $percentage213 = $result->where('grade', '21.3');
            if($percentage213->isEmpty())
            {
                $sumPercentage213[] = 0;
            } else {
                foreach($percentage213 as $per213)
                {
                    $sumPercentage213[] = $per213->percentage_analysis * $per213->total_in_kg;
                }
            }
            $percentage214 = $result->where('grade', '21.4');
            if($percentage214->isEmpty())
            {
                $sumPercentage214[] = 0;
            } else {
                foreach($percentage214 as $per214)
                {
                    $sumPercentage214[] = $per214->percentage_analysis * $per214->total_in_kg;
                }
            }
            $percentage215 = $result->where('grade', '21.5');
            if($percentage215->isEmpty())
            {
                $sumPercentage215[] = 0;
            } else {
                foreach($percentage215 as $per215)
                {
                    $sumPercentage215[] = $per215->percentage_analysis * $per215->total_in_kg;
                }
            }
            $percentage216 = $result->where('grade', '21.6');
            if($percentage216->isEmpty())
            {
                $sumPercentage216[] = 0;
            } else {
                foreach($percentage216 as $per216)
                {
                    $sumPercentage216[] = $per216->percentage_analysis * $per216->total_in_kg;
                }
            }
            $percentage217 = $result->where('grade', '21.7');
            if($percentage217->isEmpty())
            {
                $sumPercentage217[] = 0;
            } else {
                foreach($percentage217 as $per217)
                {
                    $sumPercentage217[] = $per217->percentage_analysis * $per217->total_in_kg;
                }
            }
            $percentage218 = $result->where('grade', '21.8');
            if($percentage218->isEmpty())
            {
                $sumPercentage218[] = 0;
            } else {
                foreach($percentage218 as $per218)
                {
                    $sumPercentage218[] = $per218->percentage_analysis * $per218->total_in_kg;
                }
            }
            $percentage219 = $result->where('grade', '21.9');
            if($percentage219->isEmpty())
            {
                $sumPercentage219[] = 0;
            } else {
                foreach($percentage219 as $per219)
                {
                    $sumPercentage219[] = $per219->percentage_analysis * $per219->total_in_kg;
                }
            }
            $percentage220 = $result->where('grade', '22.0');
            if($percentage220->isEmpty())
            {
                $sumPercentage220[] = 0;
            } else {
                foreach($percentage220 as $per220)
                {
                    $sumPercentage220[] = $per220->percentage_analysis * $per220->total_in_kg;
                }
            }
            $percentage221 = $result->where('grade', '22.1');
            if($percentage221->isEmpty())
            {
                $sumPercentage221[] = 0;
            } else {
                foreach($percentage221 as $per221)
                {
                    $sumPercentage221[] = $per221->percentage_analysis * $per221->total_in_kg;
                }
            }
            $percentage222 = $result->where('grade', '22.2');
            if($percentage222->isEmpty())
            {
                $sumPercentage222[] = 0;
            } else {
                foreach($percentage222 as $per222)
                {
                    $sumPercentage222[] = $per222->percentage_analysis * $per222->total_in_kg;
                }
            }
            $percentage223 = $result->where('grade', '22.3');
            if($percentage223->isEmpty())
            {
                $sumPercentage223[] = 0;
            } else {
                foreach($percentage223 as $per223)
                {
                    $sumPercentage223[] = $per223->percentage_analysis * $per223->total_in_kg;
                }
            }
            $percentage224 = $result->where('grade', '22.4');
            if($percentage224->isEmpty())
            {
                $sumPercentage224[] = 0;
            } else {
                foreach($percentage224 as $per224)
                {
                    $sumPercentage224[] = $per224->percentage_analysis * $per224->total_in_kg;
                }
            }
            $percentage225 = $result->where('grade', '22.5');
            if($percentage225->isEmpty())
            {
                $sumPercentage225[] = 0;
            } else {
                foreach($percentage225 as $per225)
                {
                    $sumPercentage225[] = $per225->percentage_analysis * $per225->total_in_kg;
                }
            }
            $percentage226 = $result->where('grade', '22.6');
            if($percentage226->isEmpty())
            {
                $sumPercentage226[] = 0;
            } else {
                foreach($percentage226 as $per226)
                {
                    $sumPercentage226[] = $per226->percentage_analysis * $per226->total_in_kg;
                }
            }
            $percentage227 = $result->where('grade', '22.7');
            if($percentage227->isEmpty())
            {
                $sumPercentage227[] = 0;
            } else {
                foreach($percentage227 as $per227)
                {
                    $sumPercentage227[] = $per227->percentage_analysis * $per227->total_in_kg;
                }
            }
            $percentage228 = $result->where('grade', '22.8');
            if($percentage228->isEmpty())
            {
                $sumPercentage228[] = 0;
            } else {
                foreach($percentage228 as $per228)
                {
                    $sumPercentage228[] = $per228->percentage_analysis * $per228->total_in_kg;
                }
            }
            $percentage229 = $result->where('grade', '22.9');
            if($percentage229->isEmpty())
            {
                $sumPercentage229[] = 0;
            } else {
                foreach($percentage229 as $per229)
                {
                    $sumPercentage229[] = $per229->percentage_analysis * $per229->total_in_kg;
                }
            }
            $percentage230 = $result->where('grade', '23.0');
            if($percentage230->isEmpty())
            {
                $sumPercentage230[] = 0;
            } else {
                foreach($percentage230 as $per230)
                {
                    $sumPercentage230[] = $per230->percentage_analysis * $per230->total_in_kg;
                }
            }
            $percentage231 = $result->where('grade', '23.1');
            if($percentage231->isEmpty())
            {
                $sumPercentage231[] = 0;
            } else {
                foreach($percentage231 as $per231)
                {
                    $sumPercentage231[] = $per231->percentage_analysis * $per231->total_in_kg;
                }
            }
            $percentage232 = $result->where('grade', '23.2');
            if($percentage232->isEmpty())
            {
                $sumPercentage232[] = 0;
            } else {
                foreach($percentage232 as $per232)
                {
                    $sumPercentage232[] = $per232->percentage_analysis * $per232->total_in_kg;
                }
            }
            $percentage233 = $result->where('grade', '23.3');
            if($percentage233->isEmpty())
            {
                $sumPercentage233[] = 0;
            } else {
                foreach($percentage233 as $per233)
                {
                    $sumPercentage233[] = $per233->percentage_analysis * $per233->total_in_kg;
                }
            }
            $percentage234 = $result->where('grade', '23.4');
            if($percentage234->isEmpty())
            {
                $sumPercentage234[] = 0;
            } else {
                foreach($percentage234 as $per234)
                {
                    $sumPercentage234[] = $per234->percentage_analysis * $per234->total_in_kg;
                }
            }

            $percentage235 = $result->where('grade', '23.5');
            if($percentage235->isEmpty())
            {
                $sumPercentage235[] = 0;
            } else {
                foreach($percentage235 as $per235)
                {
                    $sumPercentage235[] = $per235->percentage_analysis * $per235->total_in_kg;
                }
            }

            $totalPercentage210 = array_sum($sumPercentage210); 
            $totalPercentage211 = array_sum($sumPercentage211); 
            $totalPercentage212 = array_sum($sumPercentage212); 
            $totalPercentage213 = array_sum($sumPercentage213); 
            $totalPercentage214 = array_sum($sumPercentage214); 
            $totalPercentage215 = array_sum($sumPercentage215);
            $totalPercentage216 = array_sum($sumPercentage216);
            $totalPercentage217 = array_sum($sumPercentage217);
            $totalPercentage218 = array_sum($sumPercentage218);
            $totalPercentage219 = array_sum($sumPercentage219); 
            $totalPercentage220 = array_sum($sumPercentage220); 
            $totalPercentage221 = array_sum($sumPercentage221);
            $totalPercentage222 = array_sum($sumPercentage222);
            $totalPercentage223 = array_sum($sumPercentage223); 
            $totalPercentage224 = array_sum($sumPercentage224);
            $totalPercentage225 = array_sum($sumPercentage225); 
            $totalPercentage226 = array_sum($sumPercentage226);
            $totalPercentage227 = array_sum($sumPercentage227);
            $totalPercentage228 = array_sum($sumPercentage228);
            $totalPercentage229 = array_sum($sumPercentage229); 
            $totalPercentage230 = array_sum($sumPercentage230);
            $totalPercentage231 = array_sum($sumPercentage231);
            $totalPercentage232 = array_sum($sumPercentage232);
            $totalPercentage233 = array_sum($sumPercentage233); 
            $totalPercentage234 = array_sum($sumPercentage234);
            $totalPercentage235 = array_sum($sumPercentage235);

            $totalPercentage = $totalPercentage210 + $totalPercentage211 + $totalPercentage212 + $totalPercentage213 + $totalPercentage214 + $totalPercentage215 + $totalPercentage216 + $totalPercentage217 + $totalPercentage218 + $totalPercentage219 +  $totalPercentage220 + $totalPercentage221 + $totalPercentage222 + $totalPercentage223 + $totalPercentage224 + $totalPercentage225 + $totalPercentage226 + $totalPercentage227 + $totalPercentage228 + $totalPercentage229 + $totalPercentage230 + $totalPercentage231 + $totalPercentage232 + $totalPercentage233 + $totalPercentage234 + $totalPercentage235;
            
            $percentageAverage = $totalPercentage / $totalKG;

            $totalPercentageAverage = number_format((float)$percentageAverage, 2, '.', '');

            // 
            $bags = $totalKG / 50;
            $str_arr = explode('.',$bags);
            $str = str_replace($str_arr[0], '0.', $str_arr[0]);
            $strP = $str_arr[1] ?? 0;
            $substr = $str.''.$strP;
            $answer = $substr * 50;
            $totalBags = [
                'bags' => $str_arr[0],
                'pounds' => number_format((float)$answer, 0, '.', '')
            ];
  
            $totalAmountPayable = $result->sum('price');
            $totalQualityInKg = $result->sum('total_in_kg');

            $avgPrice = $totalAmountPayable / $totalQualityInKg;
            $averagePrice = number_format((float)$avgPrice, 2, '.', '');
  
            $data = ['TOTAL_BAGS' => $totalBags, 'AB' => $totalBeratingAverage, 'AP' => $totalPercentageAverage, 'TAP' => $totalAmountPayable, 'AVGPRICE' => $averagePrice];
        }

        if (request()->ajax()) {
            return DataTables::of($analysis)->make(true);
        }

        return view('assistant_managers.weekly_material_summary.low_grade_kg', [
            'analysis' => $analysis,
            'data' => $data,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'manager' => $request->manager
        ]);
    }   

    // Rates List
    public function rates_berating()
    {
        return view('assistant_managers.berating-rate.view');
    }

    public function add_rate_berating()
    {
        return view('assistant_managers.berating-rate.add');
    }

    public function post_rate_berating(Request $request)
    {
        $response = [
            'grade.regex' => 'Grade field requires decimal point.'
        ];

        $this->validate($request, [
            'grade' => ['required', 'numeric', 'unique:berating_calculations', 'regex:/^[-+]?[0-9]+\.[0-9]+$/'],
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'], 
        ], $response);

        BeratingCalculation::create([
            'grade' => $request->grade,
            'price' => $request->price,
            'unit_price' => $request->unit_price
        ]);

        return back()->with([
            'alertType' => 'success',
            'back' => route('assistant_managers.rates.berating'),
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_berating_update($id, Request $request)
    {
        $this->validate($request, [
            'price' => ['required', 'numeric'],
            'unit_price' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);
        
        $beratingcalculation = BeratingCalculation::find($finder);

        if($beratingcalculation->grade == $request->grade)
        {
            $beratingcalculation->update([
                'grade' => $request->grade,
                'price' => $request->price,
                'unit_price' => $request->unit_price
            ]);
    
        } else {
            $this->validate($request, [
                'grade' => ['required', 'numeric', 'unique:berating_calculations'],
            ]);

            $beratingcalculation->update([
                'grade' => $request->grade,
                'price' => $request->price,
                'unit_price' => $request->unit_price
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function rate_berating_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $beratingcalculation = BeratingCalculation::find($finder);

        $beratingcalculation->update([
            'status' => 'Active'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Activated successfully!'
        ]);
    }

    public function rate_berating_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $beratingcalculation = BeratingCalculation::find($finder);

        $beratingcalculation->update([
            'status' => 'Inactive'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deactivated successfully!'
        ]);
    }

    public function rate_berating_delete($id)
    {
        $finder = Crypt::decrypt($id);

        $rate = BeratingCalculation::find($finder);

        $payment_analysis_tin = PaymentReceiptTin::where('grade', $rate->id)->get();
        $payment_analysis_columbite = PaymentReceiptColumbite::where('grade', $rate->id)->get();

        if($payment_analysis_tin->count() > 0)
        {
            return back()->with([
                'type' => 'danger',
                'message' => "Sorry, rate can't be deleted, it has been used on a payment receipt."
            ]);
        }

        if($payment_analysis_columbite->count() > 0)
        {
            return back()->with([
                'type' => 'danger',
                'message' => "Sorry, rate can't be deleted, it has been used on a payment receipt."
            ]);
        }

        $rate->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    public function rates_analysis()
    {
        return view('assistant_managers.analysis-rate.view');
    }

    public function add_rate_analysis()
    {
        return view('assistant_managers.analysis-rate.add');
    }

    public function post_rate_analysis(Request $request)
    {
        $this->validate($request, [
            'percentage_min' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            'percentage_max' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);
        
       AnalysisCalculation::create([
            'percentage_min' => $request->percentage_min,
            'percentage_max' => $request->percentage_max,
            'dollar_rate' => $request->dollar,
            'exchange_rate' => $request->exchange
        ]);

        return back()->with([
            'alertType' => 'success',
            'back' => route('assistant_managers.rates.analysis'),
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_analysis_update($id, Request $request)
    {
        $this->validate($request, [
            'dollar' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);
        

        if($analysiscalculation->grade == $request->grade)
        {
            $analysiscalculation->update([
                'percentage_min' => $request->percentage_min,
                'percentage_max' => $request->percentage_max,
                'dollar_rate' => $request->dollar,
                'exchange_rate' => $request->exchange
            ]);

        } else {
            $this->validate($request, [
                'percentage_min' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
                'percentage_max' => ['required', 'string', 'max:255', 'unique:analysis_calculations'],
            ]);

            $analysiscalculation->update([
                'percentage_min' => $request->percentage_min,
                'percentage_max' => $request->percentage_max,
                'dollar_rate' => $request->dollar,
                'exchange_rate' => $request->exchange
            ]);
        }

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function rate_analysis_activate($id)
    {
        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);

        $analysiscalculation->update([
            'status' => 'Active'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Activated successfully!'
        ]);
    }

    public function rate_analysis_deactivate($id)
    {
        $finder = Crypt::decrypt($id);

        $analysiscalculation = AnalysisCalculation::find($finder);

        $analysiscalculation->update([
            'status' => 'Inactive'
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deactivated successfully!'
        ]);
    }

    public function rate_analysis_delete($id)
    {
        $finder = Crypt::decrypt($id);

        AnalysisCalculation::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }

    public function rates_beanchmark()
    {
        $benchmark = Benchmark::latest()->get();

        return view('assistant_managers.benchmark.view', [
            'benchmark' => $benchmark
        ]);
    }

    public function post_rate_benchmark(Request $request)
    {
        $this->validate($request, [
            'amount' => ['required', 'numeric'],
        ]);

        $result = ($request->amount / 70 * fixed_rate) / 70;

        $bench = number_format((float)$result, 2, '.', '');
        
        Benchmark::create([
            'amount' => $request->amount,
            'benchmark_value' => $bench,
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Added successfully!'
        ]);
    }

    public function rate_benchmark_update($id, Request $request)
    {
        $this->validate($request, [
            'amount' => ['required', 'numeric'],
        ]);

        $finder = Crypt::decrypt($id);

        $benchmark = Benchmark::find($finder);

        $result = ($request->amount / 70 * fixed_rate) / 70;

        $bench = number_format((float)$result, 2, '.', '');

        $benchmark->update([
            'amount' => $request->amount,
            'benchmark_value' => $bench
        ]);

        return back()->with([
            'alertType' => 'success',
            'message' => 'Updated successfully!'
        ]);
    }

    public function rate_benchmark_delete($id, Request $request)
    {
        $finder = Crypt::decrypt($id);

        Benchmark::find($finder)->delete();

        return back()->with([
            'alertType' => 'success',
            'message' => 'Deleted successfully!'
        ]);
    }
}
