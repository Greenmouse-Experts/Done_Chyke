@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">All Lower Grade Columbite Payment Receipt</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lower Grade Columbite Payment Receipt</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                    <a href="{{route('payment.receipt.lower.grade.columbite.add', 'pound')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link @if($active_tab == 'pound') active @endif" id="pills-pound-tab" data-toggle="pill" href="#pills-pound" role="tab" aria-controls="pills-pound" aria-selected="true">POUND</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link  @if($active_tab == 'kg') active @endif" id="pills-kg-tab" data-toggle="pill" href="#pills-kg" role="tab" aria-controls="pills-kg" aria-selected="false">KG</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-2">
                            <div class="tab-pane fade @if($active_tab == 'pound') active show @endif" id="pills-pound" role="tabpanel" aria-labelledby="pills-pound-tab">
                                <div class="table-responsive rounded mb-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                                        <form action="{{ route('payment.receipt.lower.grade.columbite.view', 'pound')}}" method="POST" data-toggle="validator">
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
                                                <th>Supplier Name</th>
                                                <th>Type of Material</th>
                                                <th>Manager</th>
                                                <th>Grade</th>
                                                <!-- <th>Berating Rate List</th> -->
                                                <th>Bags</th>
                                                <th>Pound</th>
                                                <th>Percentage (%)</th>
                                                <th>% Analysis Rate List</th>
                                                <th>Total Quantity In Pound</th>
                                                <th>Receipt Image</th>
                                                <th>Total Amount Payable</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach($lowergradecolumbitePaymentReceiptPound as $receipt)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>{{$receipt->supplier}}</td>
                                                <td>@if($receipt->type == 'kg')Columbite (KG) @else Columbite (POUND) @endif</td>
                                                <td>
                                                    @if (App\Models\User::where('id', $receipt->staff)->exists())
                                                    {{App\Models\User::find($receipt->staff)->name}}
                                                    @else
                                                    <b>{{ 'USER DELETED' }}</b>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (App\Models\BeratingCalculation::where('id', $receipt->grade)->exists())
                                                    {{App\Models\BeratingCalculation::find($receipt->grade)->grade}}
                                                    @else
                                                    <b>{{ 'GRADE DELETED' }}</b>
                                                    @endif
                                                </td>
                                                <!-- <td>
                                                    @foreach(json_decode($receipt->berating_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td> -->
                                                <td>{{$receipt->bag}}</td>
                                                <td>{{$receipt->pound}}</td>
                                                <th>{{$receipt->percentage_analysis}}</th>
                                                <td>
                                                    @foreach(json_decode($receipt->analysis_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>{{$receipt->total_in_pound}}lbs</td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#preview-{{$receipt->id}}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Image" data-original-title="Preview Receipt Image"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}"></a>
                                                    </span>
                                                </td>
                                                <td>₦{{number_format($receipt->price, 2)}}</td>

                                                <div class="modal fade" id="preview-{{$receipt->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Preview Receipt Image</h5>
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
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="11" style="font-size: 1.1rem; font-weight: 700">Total</td>
                                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">{{$lowergradecolumbitePaymentReceiptPound->sum('total_in_pound')}}lbs</td>
                                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($lowergradecolumbitePaymentReceiptPound->sum('price'), 2)}}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade @if($active_tab == 'kg') active show @endif" id="pills-kg" role="tabpanel" aria-labelledby="pills-kg-tab">
                                <div class="table-responsive rounded mb-3">
                                    <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                                        <form action="{{ route('payment.receipt.lower.grade.columbite.view', 'kg')}}" method="POST" data-toggle="validator">
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
                                                <th>Supplier Name</th>
                                                <th>Type of Material</th>
                                                <th>Manager</th>
                                                <th>Grade</th>
                                                <!-- <th>Berating Rate List</th> -->
                                                <th>Bags</th>
                                                <th>Kg</th>
                                                <th>Percentage (%)</th>
                                                <th>% Analysis Rate List</th>
                                                <th>Total Quantity In Kg</th>
                                                <th>Receipt Image</th>
                                                <th>Total Amount Payable</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach($lowergradecolumbitePaymentReceiptKg as $receipt)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>{{$receipt->supplier}}</td>
                                                <td>@if($receipt->type == 'kg')Columbite (KG) @else Columbite (POUND) @endif</td>
                                                <td>
                                                    @if (App\Models\User::where('id', $receipt->staff)->exists())
                                                    {{App\Models\User::find($receipt->staff)->name}}
                                                    @else
                                                    <b>{{ 'USER DELETED' }}</b>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (App\Models\BeratingCalculation::where('id', $receipt->grade)->exists())
                                                    {{App\Models\BeratingCalculation::find($receipt->grade)->grade}}
                                                    @else
                                                    <b>{{ 'GRADE DELETED' }}</b>
                                                    @endif
                                                </td>
                                                <!-- <td>
                                                    @foreach(json_decode($receipt->berating_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td> -->
                                                <td>{{$receipt->bag}}</td>
                                                <td>{{$receipt->kg}}</td>
                                                <th>{{$receipt->percentage_analysis}}</th>
                                                <td>
                                                    @foreach(json_decode($receipt->analysis_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>{{$receipt->total_in_kg}}kg</td>
                                                <td>
                                                    <span data-toggle="modal" data-target="#preview-{{$receipt->id}}">
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Image" data-original-title="Preview Receipt Image"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$receipt->receipt_image}}" alt="{{$receipt->receipt_image}}"></a>
                                                    </span>
                                                </td>
                                                <td>₦{{number_format($receipt->price, 2)}}</td>

                                                <div class="modal fade" id="preview-{{$receipt->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Preview Receipt Image</h5>
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
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="11" style="font-size: 1.1rem; font-weight: 700">Total</td>
                                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">{{$lowergradecolumbitePaymentReceiptKg->sum('total_in_kg')}}kg</td>
                                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($lowergradecolumbitePaymentReceiptKg->sum('price'), 2)}}</td>
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
@endsection