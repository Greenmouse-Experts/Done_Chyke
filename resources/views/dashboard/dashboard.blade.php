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
            <div class="col-lg-5">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hi {{Auth::user()->name}}, {{$moment}}</h3>
                        <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-user-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Expenses</p>
                                        <h4>{{App\Models\Expenses::get()->count()}}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="{{App\Models\Expenses::get()->count()}}">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <i class="ri-wallet-2-fill mr-0"></i>
                                    </div>
                                    <div>
                                        <p class="mb-2">Total Starting Balance - {{\Carbon\Carbon::now()->toFormattedDateString()}}</p>
                                        <h4>₦{{number_format($totalStartingBalance, 2)}}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Expenses Summary Daily</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div><a href="{{route('expenses.view')}}" class="btn btn-primary view-btn font-size-14">View Reports</a></div>
                                </div>
                            </div>
                            <div class="card-body pb-5">
                                <div class="d-flex flex-wrap align-items-center mt-2">
                                    <div class="d-flex align-items-center ml-5 progress-order-right">
                                        <div class="progress progress-round m-0 primary conversation-bar" data-percent="200">
                                            <span class="progress-left">
                                                <span class="progress-bar"></span>
                                            </span>
                                            <span class="progress-right">
                                                <span class="progress-bar"></span>
                                            </span>
                                            <div class="progress-value text-primary">{{$monthly_expenses_count}}%</div>
                                        </div>
                                        <div class="progress-value ml-3">
                                            <h5>₦{{number_format($monthly_expenses, 2)}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
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
            </div>
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div class="card-transparent card-block card-stretch mb-4">
                            <div class="card-header d-flex align-items-center justify-content-between p-0">
                                <div class="header-title">
                                    <h4 class="card-title mb-0">Expenses</h4>
                                </div>
                                <div class="card-header-toolbar d-flex align-items-center">
                                    <div><a href="{{route('expenses.view')}}" class="btn btn-primary view-btn font-size-14">View All</a></div>
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
                                    @foreach(App\Models\Expenses::latest()->where('user_id', Auth::user()->id)->get()->take(5) as $expense)
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
@endif

@endsection