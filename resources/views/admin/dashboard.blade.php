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
                                    <div class="icon iq-icon-box-2 bg-info-light">
                                        <i class="ri-user-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Staffs</p>
                                        <h4>{{$staffs}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-info iq-progress progress-1" data-percent="{{$staffs}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card card-block card-stretch card-height-helf">
                            <div class="card-body">
                                <div class="d-flex align-items-top justify-content-between">
                                    <div class="">
                                        <p class="mb-0">Expenses Summary</p>
                                        <h5>₦<span id="showExpenses">{{number_format($expenses, 2)}}</span></h5>
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
                            </div>
                        </div>
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Overview</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001" data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Year</a>
                                    <a class="dropdown-item" href="#">Month</a>
                                    <a class="dropdown-item" href="#">Week</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart1"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Revenue Vs Cost</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002" data-toggle="dropdown">
                                    This Month<i class="ri-arrow-down-s-line ml-1"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none" aria-labelledby="dropdownMenuButton002">
                                    <a class="dropdown-item" href="#">Yearly</a>
                                    <a class="dropdown-item" href="#">Monthly</a>
                                    <a class="dropdown-item" href="#">Weekly</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart-2" style="min-height: 360px;"></div>
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
            }
        })
    }
</script>
@endsection