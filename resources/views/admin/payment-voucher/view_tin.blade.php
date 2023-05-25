@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Tin Payment Voucher</h4>
                        <p class="mb-0">All tin payment voucher in one place </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Type</th>
                                <th>Assistant Manager</th>
                                <th>Customer Name</th>
                                <th>Manager</th>
                                <th>Berating</th>
                                <th>Bags</th>
                                <th>Pounds</th>
                                <th>Percentage (%) voucher</th>
                                <th>Receipt</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($tinPaymentVoucher as $voucher)
                            <tr>
                                <td>
                                {{$loop->iteration}}
                                </td>
                                <td>@if($voucher->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                <td><a href="{{route('admin.edit.manager.assistance', Crypt::encrypt($voucher->user_id))}}">{{App\Models\User::find($voucher->user_id)->name}}</a></td>
                                <td>{{$voucher->customer}}</td>
                                <td><a href="{{route('admin.edit.manager', Crypt::encrypt($voucher->manager_id))}}">{{App\Models\Manager::find($voucher->manager_id)->name}}</a></td>
                                <td>{{App\Models\BeratingCalculation::find($voucher->berating)->grade}}</td>
                                <td>{{$voucher->bags}}</td>
                                <td>{{$voucher->pounds}}</td>
                                <td>{{$voucher->percentage_voucher}}</td>
                                <td><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$voucher->receipt}}" alt="{{$voucher->receipt}}"></td>
                                <td>₦{{number_format($voucher->price, 2)}}</td>
                                <td>{{$voucher->date}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection