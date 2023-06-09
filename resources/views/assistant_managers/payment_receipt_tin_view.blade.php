@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Tin Payment Receipts</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tin Payment Receipts</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                    <a href="{{route('payment.receipt.tin.add', 'pound')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-pound-tab" data-toggle="pill" href="#pills-pound" role="tab" aria-controls="pills-pound" aria-selected="true">POUND</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-kg-tab" data-toggle="pill" href="#pills-kg" role="tab" aria-controls="pills-kg" aria-selected="false">KG</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-2">
                            <div class="tab-pane fade show active" id="pills-pound" role="tabpanel" aria-labelledby="pills-pound-tab">
                                <div class="table-responsive rounded mb-3">
                                    <table class="data-table table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>S/N</th>
                                                <th>Date of Purchse</th>
                                                <th>Receipt No</th>
                                                <th>Supplier Name</th>
                                                <th>Type of Material</th>
                                                <th>Manager</th>
                                                <th>Grade</th>
                                                <th>Berating Rate List</th>
                                                <th>Bags</th>
                                                <th>Pounds</th>
                                                <th>Total Quantity In Pound</th>
                                                <th>Receipt Image</th>
                                                <th>Total Amount Payable</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach(App\Models\PaymentReceiptTin::latest()->where('user_id', Auth::user()->id)->where('type', 'pound')->get() as $receipt)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>{{$receipt->supplier}}</td>
                                                <td>@if($receipt->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                                <td>{{App\Models\User::find($receipt->staff)->name}}</td>
                                                <td>{{App\Models\BeratingCalculation::find($receipt->grade)->grade}}</td>
                                                <td>
                                                    @foreach(json_decode($receipt->berating_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>{{$receipt->bag}}</td>
                                                <td>{{$receipt->pound}}</td>
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
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-kg" role="tabpanel" aria-labelledby="pills-kg-tab">
                                <div class="table-responsive rounded mb-3">
                                    <table class="data-table table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>S/N</th>
                                                <th>Date of Purchse</th>
                                                <th>Receipt No</th>
                                                <th>Supplier Name</th>
                                                <th>Type of Material</th>
                                                <th>Manager</th>
                                                <th>Grade</th>
                                                <th>Berating Rate List</th>
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
                                            @foreach(App\Models\PaymentReceiptTin::latest()->where('user_id', Auth::user()->id)->where('type', 'kg')->get() as $receipt)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>{{$receipt->date_of_purchase}}</td>
                                                <td>{{$receipt->receipt_no}}</td>
                                                <td>{{$receipt->supplier}}</td>
                                                <td>@if($receipt->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                                <td>{{App\Models\User::find($receipt->staff)->name}}</td>
                                                <td>{{App\Models\BeratingCalculation::find($receipt->grade)->grade}}</td>
                                                <td>
                                                    @foreach(json_decode($receipt->berating_rate_list, true) as $key => $value)
                                                        <p>{{ $key }} - {{ $value }}</p>
                                                    @endforeach
                                                </td>
                                                <td>{{$receipt->bag}}</td>
                                                <td>{{$receipt->kg}}</td>
                                                <td>{{$receipt->percentage_analysis}}</td>
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
                                            </tr>
                                            @endforeach
                                        </tbody>
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