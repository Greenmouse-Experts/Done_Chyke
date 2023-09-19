@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Dashboard</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hi {{Auth::user()->name}}, {{$moment}}</h3>
                        <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-user-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Staffs</p>
                                        <h4>{{$staffs}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{$staffs}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-user-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Payment Receipts</p>
                                        <h4>{{$totalReceipt}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{$totalReceipt}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Today Starting Balance</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <div><a href="{{route('admin.daily.balance')}}" class="btn btn-primary view-btn font-size-14">View All</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>₦<span id="">{{number_format($totalStartingBalance, 2)}}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Miscellaneous Expenses Summary</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton004" data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton004">
                                    <a class="dropdown-item" onclick="showYear()" href="#">Year</a>
                                    <a class="dropdown-item" onclick="showMonth()" href="#">Month</a>
                                    <a class="dropdown-item" onclick="showWeek()" href="#">Week</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>₦<span id="showExpenses">{{number_format($expenses, 2)}}</span></h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Payment Receipt Summary</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton005" data-toggle="dropdown">
                                    Today<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton005">
                                    <a class="dropdown-item" onclick="showPaymentMonth()" href="#">Month</a>
                                    <a class="dropdown-item" onclick="showPaymentWeek()" href="#">Week</a>
                                    <a class="dropdown-item" onclick="showPaymentDay()" href="#">Today</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-2">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center progres-order-left">
                                <div class="progress progress-round m-0 orange conversation-bar" data-percent="46">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-secondary" id="showTinPoundPaymentReceiptCount">{{$receiptTinPoundCount}}</div>
                                </div>
                                <div class="progress-value ml-3 pr-5 border-right">
                                    <h5>₦<span id="showTinPoundPaymentReceiptSummary">{{number_format($receiptTinPound, 2)}}</span></h5>
                                    <p class="mb-0">Total Tin (Pound)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ml-5 progress-order-right">
                                <div class="progress progress-round m-0 primary conversation-bar" data-percent="100">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-primary" id="showTinKgPaymentReceiptCount">{{$receiptTinKgCount}}</div>
                                </div>
                                <div class="progress-value ml-3 pr-5 border-right">
                                    <h5>₦<span id="showTinKgPaymentReceiptSummary">{{number_format($receiptTinKg, 2)}}</span></h5>
                                    <p class="mb-0">Total Tin (Kg)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ml-5 progress-order-right">
                                <div class="progress progress-round m-0 primary conversation-bar" data-percent="46">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-primary" id="showColumbitePoundPaymentReceiptCount">{{$receiptColumbitePoundCount}}</div>
                                </div>
                                <div class="progress-value ml-3">
                                    <h5>₦<span id="showColumbitePoundPaymentReceiptSummary">{{number_format($receiptColumbitePound, 2)}}</span></h5>
                                    <p class="mb-0">Total Columbite (Pound)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ml-5 progress-order-right">
                                <div class="progress progress-round m-0 primary conversation-bar" data-percent="46">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-primary" id="showColumbiteKgPaymentReceiptCount">{{$receiptColumbiteKgCount}}</div>
                                </div>
                                <div class="progress-value ml-3">
                                    <h5>₦<span id="showColumbiteKgPaymentReceiptSummary">{{number_format($receiptColumbiteKg, 2)}}</span></h5>
                                    <p class="mb-0">Total Columbite (Kg)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Lower Grade Summary</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton105" data-toggle="dropdown">
                                    Today<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton105">
                                    <a class="dropdown-item" onclick="showLowerGradePaymentMonth()" href="#">Month</a>
                                    <a class="dropdown-item" onclick="showLowerGradePaymentWeek()" href="#">Week</a>
                                    <a class="dropdown-item" onclick="showLowerGradePaymentDay()" href="#">Today</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-2">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-2">
                            <div class="d-flex align-items-center ml-5 progress-order-right mb-5">
                                <div class="progress progress-round m-0 primary conversation-bar" data-percent="46">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-primary" id="showLowerGradeColumbitePoundPaymentReceiptCount">{{$receiptLowerGradeColumbitePoundCount}}</div>
                                </div>
                                <div class="progress-value ml-3">
                                    <h5>₦<span id="showLowerGradeColumbitePoundPaymentReceiptSummary">{{number_format($receiptLowerGradeColumbitePound, 2)}}</span></h5>
                                    <p class="mb-0">Total Lower Grade Columbite (Pound)</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ml-5 progress-order-right">
                                <div class="progress progress-round m-0 primary conversation-bar" data-percent="46">
                                    <span class="progress-left">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <span class="progress-right">
                                        <span class="progress-bar"></span>
                                    </span>
                                    <div class="progress-value text-primary" id="showLowerGradeColumbiteKgPaymentReceiptCount">{{$receiptLowerGradeColumbiteKgCount}}</div>
                                </div>
                                <div class="progress-value ml-3">
                                    <h5>₦<span id="showLowerGradeColumbiteKgPaymentReceiptSummary">{{number_format($receiptLowerGradeColumbiteKg, 2)}}</span></h5>
                                    <p class="mb-0">Total Lower Grade Columbite (Kg)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="card-transparent card-block card-stretch mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between p-0">
                                <div class="header-title">
                                    <h4 class="card-title mb-0">Today's Transactions</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div><a href="{{route('admin.transactions')}}" class="btn btn-primary view-btn font-size-14">View All</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive rounded mb-3">
                            <table class="table mb-0 tbl-server-info">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>S/N</th>
                                        <th>Staff</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @if(App\Models\Transaction::latest()->whereDate('created_at', \Carbon\Carbon::now()->format('Y-m-d'))->get()->count() > 0)
                                        @foreach(App\Models\Transaction::latest()->whereDate('created_at', \Carbon\Carbon::now()->format('Y-m-d'))->get()->take(5) as $transaction)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                                @if (App\Models\User::where('id', $transaction->user_id)->exists())
                                                <p>{{App\Models\User::find($transaction->user_id)->account_type}}</p>
                                                {{App\Models\User::find($transaction->user_id)->name}}
                                                @else
                                                    <b>{{ 'USER DELETED' }}</b> 
                                                @endif
                                            </td>
                                            <td>₦{{number_format($transaction->amount, 2)}}</td>
                                            <td>
                                                @if($transaction->status == 'Top Up')
                                                <span class="badge bg-success">{{$transaction->status}}</span>
                                                @elseif($transaction->status == 'Expense')
                                                <span class="badge bg-danger">{{$transaction->status}}</span>
                                                @else
                                                <span class="badge bg-success">{{$transaction->status}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No Transaction added today</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="card-transparent card-block card-stretch mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between p-0">
                                <div class="header-title">
                                    <h4 class="card-title mb-0">Today's Miscellaneous Expenses</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div><a href="{{route('admin.expenses')}}" class="btn btn-primary view-btn font-size-14">View All</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive rounded mb-3">
                            <table class="table mb-0 tbl-server-info">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>S/N</th>
                                        <th>Supplier</th>
                                        <th>Collected By</th>
                                        <th>Payment Source</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @if(App\Models\Expenses::latest()->where('date', \Carbon\Carbon::now()->format('Y-m-d'))->get()->count() > 0)
                                        @foreach(App\Models\Expenses::latest()->where('date', \Carbon\Carbon::now()->format('Y-m-d'))->get()->take(5) as $expense)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>
                                            @if (App\Models\User::where('id', $expense->supplier)->exists())
                                            {{App\Models\User::find($expense->supplier)->name}}
                                            @else
                                            {{$expense->supplier_additional_field}}
                                            @endif
                                            </td>
                                            <td>{{$expense->collected_by}}</td>
                                            <td>{{$expense->payment_source}}</td>
                                            <td>{{$expense->category}}</td>
                                            <td>₦{{number_format($expense->amount, 2)}}</td>
                                            <td>{{$expense->date}}</td>
                                            <td>
                                                @if($expense->receipt == null)
                                                <p>None</p>
                                                @else
                                                <span data-toggle="modal" data-target="#preview-{{$expense->id}}">
                                                    <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Attachment" data-original-title="Preview Receipt Attachment"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$expense->receipt}}" alt="{{$expense->receipt}}"></a>
                                                </span>
                                                @endif
                                            </td>
                                            <div class="modal fade" id="preview-{{$expense->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Prview Receipt Attachment</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <img src="{{$expense->receipt}}" alt="{{$expense->receipt}}" class="img-fluid rounded">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No Expenses added today</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<script type="text/javascript">
    function showYear(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                expenses_interval: 'yearly',
            },
            success: function(result) {
                $('#dropdownMenuButton004').html('This Year');
                $('#showExpenses').html(result.expenses);
            }
        })
    }
    function showMonth(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                expenses_interval: 'monthly',
            },
            success: function(result) {
                $('#dropdownMenuButton004').html('This Month');
                $('#showExpenses').html(result.expenses);
            }
        })
    }
    function showWeek(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                expenses_interval: 'weekly',
            },
            success: function(result) {
                $('#dropdownMenuButton004').html('This Week');
                $('#showExpenses').html(result.expenses);
            }
        })
    }

    function showPaymentDay(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_interval: 'daily',
            },
            success: function(result) {
                $('#dropdownMenuButton005').html('Today');
                $('#showTinPoundPaymentReceiptCount').html(result.receiptTinPoundCount);
                $('#showTinPoundPaymentReceiptSummary').html(result.receiptTinPound);
                $('#showTinKgPaymentReceiptCount').html(result.receiptTinKgCount);
                $('#showTinKgPaymentReceiptSummary').html(result.receiptTinKg);
                $('#showColumbitePoundPaymentReceiptCount').html(result.receiptColumbitePoundCount);
                $('#showColumbitePoundPaymentReceiptSummary').html(result.receiptColumbitePound);
                $('#showColumbiteKgPaymentReceiptCount').html(result.receiptColumbiteKgCount);
                $('#showColumbiteKgPaymentReceiptSummary').html(result.receiptColumbiteKg);
            }
        })
    }
    function showPaymentMonth(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_interval: 'monthly',
            },
            success: function(result) {
                $('#dropdownMenuButton005').html('This Month');
                $('#showTinPoundPaymentReceiptCount').html(result.receiptTinPoundCount);
                $('#showTinPoundPaymentReceiptSummary').html(result.receiptTinPound);
                $('#showTinKgPaymentReceiptCount').html(result.receiptTinKgCount);
                $('#showTinKgPaymentReceiptSummary').html(result.receiptTinKg);
                $('#showColumbitePoundPaymentReceiptCount').html(result.receiptColumbitePoundCount);
                $('#showColumbitePoundPaymentReceiptSummary').html(result.receiptColumbitePound);
                $('#showColumbiteKgPaymentReceiptCount').html(result.receiptColumbiteKgCount);
                $('#showColumbiteKgPaymentReceiptSummary').html(result.receiptColumbiteKg);
            }
        })
    }
    function showPaymentWeek(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_interval: 'weekly',
            },
            success: function(result) {
                $('#dropdownMenuButton005').html('This Week');
                $('#showTinPoundPaymentReceiptCount').html(result.receiptTinPoundCount);
                $('#showTinPoundPaymentReceiptSummary').html(result.receiptTinPound);
                $('#showTinKgPaymentReceiptCount').html(result.receiptTinKgCount);
                $('#showTinKgPaymentReceiptSummary').html(result.receiptTinKg);
                $('#showColumbitePoundPaymentReceiptCount').html(result.receiptColumbitePoundCount);
                $('#showColumbitePoundPaymentReceiptSummary').html(result.receiptColumbitePound);
                $('#showColumbiteKgPaymentReceiptCount').html(result.receiptColumbiteKgCount);
                $('#showColumbiteKgPaymentReceiptSummary').html(result.receiptColumbiteKg);
            }
        })
    }

    function showLowerGradePaymentDay(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_lower_grade: 'daily',
            },
            success: function(result) {
                $('#dropdownMenuButton105').html('Today');
                $('#showLowerGradeColumbitePoundPaymentReceiptCount').html(result.receiptLowerGradeColumbitePoundCount);
                $('#showLowerGradeColumbitePoundPaymentReceiptSummary').html(result.receiptLowerGradeColumbitePound);
                $('#showLowerGradeColumbiteKgPaymentReceiptCount').html(result.receiptLowerGradeColumbiteKgCount);
                $('#showLowerGradeColumbiteKgPaymentReceiptSummary').html(result.receiptLowerGradeColumbiteKg);
            }
        })
    }
    function showLowerGradePaymentMonth(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_lower_grade: 'monthly',
            },
            success: function(result) {
                $('#dropdownMenuButton105').html('This Month');
                $('#showLowerGradeColumbitePoundPaymentReceiptCount').html(result.receiptLowerGradeColumbitePoundCount);
                $('#showLowerGradeColumbitePoundPaymentReceiptSummary').html(result.receiptLowerGradeColumbitePound);
                $('#showLowerGradeColumbiteKgPaymentReceiptCount').html(result.receiptLowerGradeColumbiteKgCount);
                $('#showLowerGradeColumbiteKgPaymentReceiptSummary').html(result.receiptLowerGradeColumbiteKg);
            }
        })
    }
    function showLowerGradePaymentWeek(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            method: "get",
            data: {
                receipt_lower_grade: 'weekly',
            },
            success: function(result) {
                $('#dropdownMenuButton105').html('This Week');
                $('#showLowerGradeColumbitePoundPaymentReceiptCount').html(result.receiptLowerGradeColumbitePoundCount);
                $('#showLowerGradeColumbitePoundPaymentReceiptSummary').html(result.receiptLowerGradeColumbitePound);
                $('#showLowerGradeColumbiteKgPaymentReceiptCount').html(result.receiptLowerGradeColumbiteKgCount);
                $('#showLowerGradeColumbiteKgPaymentReceiptSummary').html(result.receiptLowerGradeColumbiteKg);
            }
        })
    }
</script>
@endsection