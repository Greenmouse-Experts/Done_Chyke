@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Make Payment</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item">@if($receiptTITLE == 'Tin')<a href="{{route('payments.tin.view', $receiptTYPE)}}">Tin Receipt</a>@elseif($receiptTITLE == 'Columbite')<a href="{{route('payments.columbite.view', $receiptTYPE)}}">Columbite Receipt</a>@else<a href="{{route('payments.lower.grade.columbite.view', $receiptTYPE)}}">Lower Grade Columbite</a>@endif</li>
                    <li class="breadcrumb-item active" aria-current="page">Make Payment</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="popup text-center mb-5 mt-4">
                            <h4 class="mb-3">Total Amount Payable: ₦{{number_format($receipt->price, 2)}}</h4>
                        </div>
                        @if($type == null)
                        <form class="text-left" action="{{route('payments.process.make', [Crypt::encrypt($receiptID), Crypt::encrypt($receiptTYPE), Crypt::encrypt($receiptTITLE)])}}" method="POST" data-toggle="validator">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Payment Action *</label>
                                        <select name="payment_action" class="selectpicker form-control" data-style="py-0" required>
                                            <option value="">-- Select Payment Action --</option>
                                            <option value="Full Payment">Full Payment</option>
                                            <option value="Part Payment">Part Payment</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Payment Type *</label>
                                        <select name="payment_type" class="selectpicker form-control" data-style="py-0" required>
                                            <option value="">-- Select Payment Type --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Direct Transfer">Direct Transfer</option>
                                            <option value="Transfer by Cheques">Transfer by Cheques</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount Paid *</label>
                                        <input type="number" class="form-control" placeholder="Enter amount" name="payment_amount" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date Paid *</label>
                                        <input type="date" class="form-control" placeholder="Enter date" name="date_paid" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>


                                @if($type <> null)
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="popup text-center mb-5 mt-4">
                                                <h4 class="mb-3">Remaining Balance: ₦{{number_format($receipt->price - $paymentAmount, 2)}}</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Final Payment Type *</label>
                                            <select name="final_payment_type" class="selectpicker form-control" data-style="py-0" @if($type <> null) required @endif>
                                                <option value="">-- Select Final Payment Type --</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Direct Transfer">Direct Transfer</option>
                                                <option value="Transfer by Cheques">Transfer by Cheques</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Final Amount Paid *</label>
                                            <input type="number" class="form-control" placeholder="Enter amount" name="final_payment_amount" @if($type <> null) required @endif>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Final Date Paid *</label>
                                            <input type="date" class="form-control" placeholder="Enter date" name="final_date_paid" @if($type <> null) required @endif>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    @endif
                            </div>
                            <div class="mt-5 text-center">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </form>
                        @endif
                        @if($type <> null)
                        <form class="text-left" action="{{route('payments.process.make', [Crypt::encrypt($receiptID), Crypt::encrypt($receiptTYPE), Crypt::encrypt($receiptTITLE)])}}" method="POST" data-toggle="validator">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="popup text-center mb-5 mt-4">
                                            <h4 class="mb-3">Remaining Balance: ₦{{number_format($receipt->price - $paymentAmount, 2)}}</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Final Payment Type *</label>
                                        <select name="final_payment_type" class="selectpicker form-control" data-style="py-0" required>
                                            <option value="">-- Select Final Payment Type --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Direct Transfer">Direct Transfer</option>
                                            <option value="Transfer by Cheques">Transfer by Cheques</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Final Amount Paid *</label>
                                        <input type="number" class="form-control" placeholder="Enter amount" name="final_payment_amount" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Final Date Paid *</label>
                                        <input type="date" class="form-control" placeholder="Enter date" name="final_date_paid" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 text-center">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection