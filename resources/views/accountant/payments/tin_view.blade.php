@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Tin Payments</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tin Payments</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 'pound') active @endif" id="pills-pound-tab" data-toggle="pill" href="#pills-pound" role="tab" aria-controls="pills-pound" aria-selected="true">POUND</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 'kg') active @endif" id="pills-kg-tab" data-toggle="pill" href="#pills-kg" role="tab" aria-controls="pills-kg" aria-selected="false">KG</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-2">
                            <div class="tab-pane fade @if($active_tab == 'pound') active show @endif" id="pills-pound" role="tabpanel" aria-labelledby="pills-pound-tab">
                                <div class="table-responsive rounded mb-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                                        <form action="{{ route('payments.tin.view', 'pound')}}" method="POST" data-toggle="validator">
                                            @csrf
                                            <label class="mr-2"><strong>Start Date :</strong>
                                            <input type="date" name="start_date" value="{{$start_date}}" class="form-control" required >
                                            </label>&nbsp;&nbsp;
                                            <label class="mr-2"><strong>End Date :</strong>
                                            <input type="date" name="end_date" value="{{$end_date}}" class="form-control" required>
                                            </label>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </form>
                                    </div>
                                    <table class="data-table table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>S/N</th>
                                                <th>Date of Purchase</th>
                                                <th>Receipt No</th>
                                                <th>Receipt Image</th>
                                                <th>Total Amount Payable</th>
                                                <th>Payment Action</th>
                                                <th>Payment Type</th>
                                                <th>Total Amount Paid</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach($tinPaymentReceiptPound as $receipt)
                                            @php
                                                if(App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'pound', 'receipt_id' => $receipt->id])->exists())
                                                {
                                                    $poundReceipt = App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'pound', 'receipt_id' => $receipt->id])->first();
                                                    $totalReceipt = App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'pound', 'receipt_id' => $receipt->id])->get();
                                                    $totalpaymentAmount = $totalReceipt->sum('payment_amount') + $totalReceipt->sum('final_payment_amount');
                                                    $action = $poundReceipt->payment_action;
                                                    $amount = '₦'.number_format($totalpaymentAmount, 2);
                                                    $type = $poundReceipt->payment_type ?? null;
                                                    $id = $poundReceipt->id;
                                                } else {
                                                    $action = 'No Payment';
                                                    $amount = '₦0';
                                                    $type = null;
                                                    $id = null;
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#preview-{{$receipt->id}}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Image" data-original-title="Preview Receipt Image"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}"></a>
                                                    </span>
                                                </td>
                                                <td>₦{{number_format($receipt->price, 2)}}</td>
                                                <td>
                                                    {{$action}}
                                                </td>
                                                <td>
                                                   {{$type}}
                                                </td>
                                                <td>
                                                   {{$amount}}
                                                </td>
                                                <div class="modal fade" id="preview-{{$receipt->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Prview Receipt Image</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <img src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}" class="img-fluid rounded">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        @if($amount === '₦'.number_format($receipt->price, 2))
                                                        <a class="badge bg-success mr-2" href="#">Payment Completed</a>
                                                        @else
                                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Make Payment" data-original-title="Make Payment" href="{{route('payments.process', [Crypt::encrypt($receipt->id), Crypt::encrypt('pound'), Crypt::encrypt('Tin')])}}">Make Payment</a>
                                                        @endif
                                                        @if($id <> null)
                                                        <a class="badge badge-primary mr-2" data-toggle="tooltip" data-placement="top" title="View Payment Details" data-original-title="View Payment Details" href="{{route('payments.view.details', Crypt::encrypt($id))}}">View Details</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="font-size: 1.1rem; font-weight: 700">Total</td>
                                                <td colspan="5" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($tinPaymentReceiptPound->sum('price'), 2)}}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade @if($active_tab == 'kg') active show @endif" id="pills-kg" role="tabpanel" aria-labelledby="pills-kg-tab">
                                <div class="table-responsive rounded mb-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                                        <form action="{{ route('payments.tin.view', 'kg')}}" method="POST" data-toggle="validator">
                                            @csrf
                                            <label class="mr-2"><strong>Start Date :</strong>
                                            <input type="date" name="start_date" value="{{$start_date}}" class="form-control" required>
                                            </label>&nbsp;&nbsp;
                                            <label class="mr-2"><strong>End Date :</strong>
                                            <input type="date" name="end_date" value="{{$end_date}}" class="form-control" required>
                                            </label>
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </form>
                                    </div>
                                    <table class="data-table table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>S/N</th>
                                                <th>Date of Purchase</th>
                                                <th>Receipt No</th>
                                                <th>Receipt Image</th>
                                                <th>Total Amount Payable</th>
                                                <th>Payment Action</th>
                                                <th>Payment Type</th>
                                                <th>Total Amount Paid</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach($tinPaymentReceiptKg as $receipt)
                                            @php
                                                if(App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'kg', 'receipt_id' => $receipt->id])->exists())
                                                {
                                                    $kgReceipt = App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'kg', 'receipt_id' => $receipt->id])->first();
                                                    $kgtotalReceipt = App\Models\Payment::where(['receipt_title' => 'Tin', 'receipt_type' => 'kg', 'receipt_id' => $receipt->id])->get();
                                                    $kgtotalpaymentAmount = $kgtotalReceipt->sum('payment_amount') + $kgtotalReceipt->sum('final_payment_amount');
                                                    $kgaction = $kgReceipt->payment_action;
                                                    $kgamount = '₦'.number_format($kgtotalpaymentAmount, 2);
                                                    $kgtype = $kgReceipt->payment_type ?? null;
                                                    $kgid = $kgReceipt->id;
                                                } else {
                                                    $kgaction = 'No Payment';
                                                    $kgamount = '₦0';
                                                    $kgtype = null;
                                                    $kgid = null;
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#preview-{{$receipt->id}}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Image" data-original-title="Preview Receipt Image"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}"></a>
                                                    </span>
                                                </td>
                                                <td>₦{{number_format($receipt->price, 2)}}</td>
                                                <td>
                                                    {{$kgaction}}
                                                </td>
                                                <td>
                                                   {{$kgtype}}
                                                </td>
                                                <td>
                                                   {{$kgamount}}
                                                </td>
                                                    <div class="modal fade" id="preview-{{$receipt->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Prview Receipt Image</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <img src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}" class="img-fluid rounded">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <td>
                                                    <div class="d-flex align-items-center list-action">
                                                        @if($kgamount === '₦'.number_format($receipt->price, 2))
                                                        <a class="badge bg-success mr-2" href="#">Payment Completed</a>
                                                        @else
                                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Make Payment" data-original-title="Make Payment" href="{{route('payments.process', [Crypt::encrypt($receipt->id), Crypt::encrypt('kg'), Crypt::encrypt('Tin')])}}">Make Payment</a>
                                                        @endif
                                                        @if($kgid <> null)
                                                        <a class="badge badge-primary mr-2" data-toggle="tooltip" data-placement="top" title="View Payment Details" data-original-title="View Payment Details" href="{{route('payments.view.details', Crypt::encrypt($kgid))}}">View Details</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="font-size: 1.1rem; font-weight: 700">Total</td>
                                                <td colspan="5" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($tinPaymentReceiptKg->sum('price'), 2)}}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<style>
    /* Styles for the modal container */
    .modals {
        position: fixed;
        right: 0px; /* Initially, hide the modal to the left */
        top: 0px;
        /* width: 100%;
        height: 100%; */
        z-index: 1;
        overflow: hidden;
        transition: left 0.3s ease; /* Slide animation duration */
    }

    @media (min-width: 768px) {
        .modals {
            top: 0px !important;
        }
    }
</style>
@endsection