@extends('layouts.dashboard_frontend')

@section('page-content')
@if(Auth::user()->account_type == 'Assistant Manager' || Auth::user()->account_type == 'Store Personnel')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Dashboard</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
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
                    <div class="col-lg-6 col-md-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-user-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Tin Payment Receipt (Pound)</p>
                                        <h4>{{App\Models\PaymentReceiptTin::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptTin::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-wallet-2-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Tin Payment Receipt (Kg)</p>
                                        <h4>{{App\Models\PaymentReceiptTin::latest()->where(['user_id' => Auth::user()->id, 'type' => 'kg'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptTin::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-flask-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Columbite Payment Receipt (Pound)</p>
                                        <h4>{{App\Models\PaymentReceiptColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-flask-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Columbite Payment Receipt (Kg)</p>
                                        <h4>{{App\Models\PaymentReceiptColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'kg'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'kg'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-flask-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Lower Grade Columbite Payment Receipt (Pound)</p>
                                        <h4>{{App\Models\PaymentReceiptLowerGradeColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptLowerGradeColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'pound'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-flask-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Count Of Lower Grade Columbite Payment Receipt (Kg)</p>
                                        <h4>{{App\Models\PaymentReceiptLowerGradeColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'kg'])->get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\PaymentReceiptLowerGradeColumbite::latest()->where(['user_id' => Auth::user()->id, 'type' => 'kg'])->get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card-transparent card-block card-stretch mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Notifications</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div><a href="{{route('notifications')}}" class="btn btn-primary view-btn font-size-14">View All</a></div>
                        </div>
                    </div>
                </div>
                <div class="card card-block card-stretch card-height-helf">
                    <div class="card-body card-item-right">
                        @if($notifications->isEmpty())
                        <p>No Notification</p>
                        @else
                        <div class="table-responsive rounded mb-3">
                            <table class="data-table table mb-0 tbl-server-info">
                                <tbody class="ligth-body">
                                    @foreach($notifications as $notification)
                                    <tr>
                                        <td>
                                            @if($notification->status == 'Unread')
                                            <div class="text-left toast fade show bg-primary text-white border-0 rounded p-2" role="alert" aria-live="assertive" aria-atomic="true">
                                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to read notification" href="{{route('read.notification', Crypt::encrypt($notification->id))}}">
                                                    <div class="toast-header bg-primary text-white">
                                                        <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                                            <rect width="100%" height="100%" fill="#fff"></rect>
                                                        </svg>
                                                        <strong class="mr-auto text-white">{{$notification->title}}</strong>
                                                        <small class="text-white">{{$notification->created_at->diffForHumans()}}</small>
                                                        <button type="button" class="mb-1 close text-white" style="font-size: 14px !important;" data-dismiss="toast" aria-label="Close">
                                                            <span aria-hidden="true" class="text-white">
                                                                @if($notification->admin_id == null)
                                                                @else
                                                                {{App\Models\User::find($notification->admin_id)->name}}
                                                                @endif
                                                            </span>
                                                        </button>
                                                    </div>
                                                    <div class="toast-body text-white">
                                                        {{$notification->body}}
                                                    </div>
                                                </a>
                                            </div>
                                            @endif
                                            @if($notification->status == 'Read')
                                            <div class="text-left toast fade show bg-success text-white border-0 rounded p-2 mt-3" role="alert" aria-live="assertive" aria-atomic="true">
                                                <div class="toast-header bg-success text-white">
                                                    <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                                        <rect width="100%" height="100%" fill="#fff"></rect>
                                                    </svg>
                                                    <strong class="mr-auto text-white">{{$notification->title}}</strong>
                                                    <small>{{$notification->created_at->diffForHumans()}}</small>
                                                    <button type="button" class="mb-1 close text-white" style="font-size: 14px !important;" data-dismiss="toast" aria-label="Close">
                                                        <span aria-hidden="true" class="text-white">
                                                            @if($notification->admin_id == null)
                                                            @else
                                                            {{App\Models\User::find($notification->admin_id)->name}}
                                                            @endif
                                                        </span>
                                                    </button>
                                                </div>
                                                <div class="toast-body">
                                                    {{$notification->body}}
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endif
@if(Auth::user()->account_type == 'Accountant')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h4 class="mb-3">Dashboard</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> <a href="{{route('daily.balance')}}" class="btn btn-primary text-white text-sm add-list"><i class="las la-plus"></i><small>Add Starting Balance</small></a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="Hamzat">
                    <h1>Starting Balance In Cash</h1> <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1695639149/money_ryutxu.png" draggable="false" alt="">
                    <p>
                        Amount: <span>₦{{number_format($totalStartingBalance, 2)}}</span>
                    </p>
                    <p>
                        Date: <span> {{\Carbon\Carbon::now()->toFormattedDateString()}} </span>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="Hamzat">
                    <h1>Expenses In Cash</h1> <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1695640160/bank-statement_rdffzg.png" draggable="false" alt="">
                    <p>
                        Amount: <span> ₦{{number_format($expensesCash, 2)}} </span>
                    </p>
                    <p>
                        Date: <span> {{\Carbon\Carbon::now()->toFormattedDateString()}} </span>
                    </p>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="Hamzat">
                    <h1>Closing Balance </h1> <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1695639982/coins_yhpghn.png" draggable="false" alt="">
                    <p>
                        Cash: <span> ₦{{number_format($closing_balance, 2)}} </span>
                    </p>
                    <p>
                        Direct Transfer: <span> ₦{{number_format($direct_transfer, 2)}} </span>
                    </p>
                    <p>
                        Transfer by Cheque: <span> ₦{{number_format($transfer_cheque, 2)}} </span>
                    </p>
                </div>
            </div>
            <div class="col-lg-8 mb-4">
                <div class="Hamzat">
                    <div class="row">
                        <div class="col-sm-3">
                            <h6 class="card-title mb-0">Final Payments</h6>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="searchInput" placeholder="Search..">
                        </div>
                        <div class="col-sm-3">Date:{{\Carbon\Carbon::now()->toFormattedDateString()}}</div>
                        <!-- <div class="col-sm-2">
                            <div class="dropdown show">
                                <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Receipt
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="#"></a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-sm-12">
                            <div class="table-responsive rounded mb-3 mt-4">
                                <table class="table mb-0 tbl-server-info" id="dataTable">
                                    <thead class="bg-white text-uppercase">
                                        <tr class="accept ligth-data">
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                        <tr>
                                            <td>Sep 25, 2023</td>
                                            <td>10,000</td>
                                            <td><i class="bi bi-eye-fill"></i> <span>
                                                    <a href="{{route('expenses.view')}}">View All</a></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="pagination">
                                    <button class="arrow" id="prevPage" disabled>← <span class="nav-text">PREV</span></button>
                                    <div class="pages">
                                        <div class="page-number active">1</div>
                                        <div class="page-number">2</div>
                                        <div class="page-number">3</div>
                                        <div class="page-number">4</div>
                                        <div class="page-number">5</div>
                                    </div>
                                    <button class="arrow" id="nextPage"><span class="nav-text">NEXT</span> →</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="Hamzat">
                    <h6 class="card-title mb-0">Notifications</h6>
                    @foreach($notifications as $notification)
                    <div class="mt-3 all">
                        <div class="toast-header">
                            @if(App\Models\User::find($notification->to)->avatar)
                            <img src="{{App\Models\User::find($notification->to)->avatar}}" draggable="false" alt="">
                            @else
                            <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1695644097/image_12_svdcew.png" draggable="false" alt="">
                            @endif
                            <strong class="mr-auto">
                                {{App\Models\User::find($notification->to)->name}}
                            </strong>
                            <small class="float-right">{{$notification->created_at->diffForHumans()}}</small>
                        </div>
                        <div class="toast-body">
                            <small> {{$notification->title}}</small>
                        </div>

                    </div>
                    @endforeach
                    <div class="text-center"><a href="#">
                            See more
                        </a></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchInput');
        var dataTable = document.getElementById('dataTable');
        var tableBody = dataTable.getElementsByTagName('tbody')[0];
        var rows = tableBody.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            var searchText = searchInput.value.toLowerCase();

            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var cells = row.getElementsByTagName('td');
                var rowText = '';

                for (var j = 0; j < cells.length; j++) {
                    rowText += cells[j].textContent.toLowerCase() + ' ';
                }

                if (rowText.indexOf(searchText) === -1) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            }
        });
    });
</script>
@endif

@endsection