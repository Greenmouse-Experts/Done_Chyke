@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">View Payment</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item">
                        @if(isset($full_payment) && $full_payment->receipt_title == 'Tin' ?? isset($part_payment) && $part_payment->receipt_title == 'Tin')
                            @if(isset($full_payment) && $full_payment->receipt_type == 'kg' ?? isset($part_payment) && $part_payment->receipt_type == 'kg')
                            <a href="{{route('payments.tin.view', 'kg')}}">Tin Receipt</a>
                            @else
                            <a href="{{route('payments.tin.view', 'pound')}}">Tin Receipt</a>
                            @endif
                        @elseif(isset($full_payment) && $full_payment->receipt_title == 'Columbite' ?? isset($part_payment) && $part_payment->receipt_title == 'Columbite')
                            @if(isset($full_payment) && $full_payment->receipt_type == 'kg' ?? isset($part_payment) && $part_payment->receipt_type == 'kg')
                            <a href="{{route('payments.columbite.view', 'kg')}}">Columbite Receipt</a>
                            @else
                            <a href="{{route('payments.columbite.view', 'pound')}}">Columbite Receipt</a>
                            @endif
                        @else
                            @if(isset($full_payment) && $full_payment->receipt_type == 'kg' ?? isset($part_payment) && $part_payment->receipt_type == 'kg')
                            <a href="{{route('payments.lower.grade.columbite.view', 'kg')}}">Lower Grade Columbite</a>
                            @else
                            <a href="{{route('payments.lower.grade.columbite.view', 'pound')}}">Lower Grade Columbite</a>
                            @endif
                        @endif
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Payment</li>
                </ol>
            </nav>
        </div>
    </div>
     <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <form class="text-left">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Payment Action *</label>
                                        <select name="payment_action" class="selectpicker form-control" data-style="py-0" readonly>
                                            <option>{{$full_payment->payment_action ?? null}}</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Payment Type *</label>
                                        <select name="payment_type" class="selectpicker form-control" data-style="py-0" readonly>
                                            <option>{{$full_payment->payment_type ?? null}}</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount Paid *</label>
                                        <input type="numeric" class="form-control" placeholder="Enter amount" value="{{$full_payment->payment_amount ?? null}}" name="payment_amount" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Paid *</label>
                                        <input type="date" class="form-control" placeholder="Enter date" value="{{$full_payment->date_paid ?? null}}" name="date_paid" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Final Payment Type *</label>
                                        <select name="final_payment_type" class="selectpicker form-control" data-style="py-0" readonly>
                                            <option>{{$part_payment->final_payment_type ?? null}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Final Amount Paid *</label>
                                        <input type="numeric" class="form-control" placeholder="Enter amount" value="{{$part_payment->final_payment_amount ?? null}}" name="final_payment_amount" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Final Date Paid *</label>
                                        <input type="date" class="form-control" placeholder="Enter date" value="{{$part_payment->final_date_paid ?? null}}" name="final_date_paid" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection