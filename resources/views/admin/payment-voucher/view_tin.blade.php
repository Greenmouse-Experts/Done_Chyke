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
                                                <th>Type</th>
                                                <th>Assistant Manager</th>
                                                <th>Customer Name</th>
                                                <th>Manager</th>
                                                <th>Berating</th>
                                                <th>Bags</th>
                                                <th>Pounds</th>
                                                <th>Total In Pounds</th>
                                                <th>Receipt</th>
                                                <th>Total Price</th>
                                                <th>Date</th>
                                                <td>Action</td>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach(App\Models\TinPaymentAnalysis::latest()->where('type', 'pound')->get() as $analysis)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>@if($analysis->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                                <td><a href="{{route('admin.edit.manager.assistance', Crypt::encrypt($analysis->user_id))}}">{{App\Models\User::find($analysis->user_id)->name}}</a></td>
                                                <td>{{$analysis->customer}}</td>
                                                <td><a href="{{route('admin.edit.manager', Crypt::encrypt($analysis->manager_id))}}">{{App\Models\Manager::find($analysis->manager_id)->name}}</a></td>
                                                <td>{{App\Models\BeratingCalculation::find($analysis->berating)->grade}}</td>
                                                <td>{{$analysis->bags}}</td>
                                                <td>{{$analysis->pounds}}</td>
                                                <td>{{$analysis->total_in_pounds}}lbs</td>
                                                <td><a href="{{config('app.url')}}{{$analysis->receipt}}" target=”_blank”><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$analysis->receipt}}" alt="{{$analysis->receipt}}"></a></td>
                                                <td>₦{{number_format($analysis->price, 2)}}</td>
                                                <td>{{$analysis->date}}</td>
                                                <td>
                                                    <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="{{route('admin.payment.voucher.tin.pound.edit', Crypt::encrypt($analysis->id))}}"><i class="ri-pencil-line mr-0"></i></a>
                                                    <span data-toggle="modal" data-target="#delete-{{$analysis->id}}">
                                                        <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                                    </span>
                                                    <div class="modal fade" id="delete-{{$analysis->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Delete</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="popup text-left">
                                                                        <h4 class="mb-3">Are you sure, you want to delete this voucher?</h4>
                                                                        <p><span class="text-danger">Note:</span> Every details attached to this coucher will be deleted and amount will be deposited back to the WALLET.</p>
                                                                        <div class="content create-workform bg-body">
                                                                            <form action="{{route('admin.expense.delete', Crypt::encrypt($analysis->id))}}" method="post">
                                                                                @csrf
                                                                                <div class="col-lg-12 mt-4">
                                                                                    <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                                                                        <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                                                                        <button type="submit" class="btn btn-primary mr-2">Delete</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
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
                                                <th>Type</th>
                                                <th>Customer Name</th>
                                                <th>Manager</th>
                                                <th>Berating</th>
                                                <th>Bags</th>
                                                <th>Kg</th>
                                                <th>Percentage (%) Analysis</th>
                                                <th>Total In Kg</th>
                                                <th>Receipt</th>
                                                <th>Total Price</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach(App\Models\TinPaymentAnalysis::latest()->where('type', 'kg')->get() as $analysis)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>@if($analysis->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                                <td><a href="{{route('admin.edit.manager.assistance', Crypt::encrypt($analysis->user_id))}}">{{App\Models\User::find($analysis->user_id)->name}}</a></td>
                                                <td>{{$analysis->customer}}</td>
                                                <td><a href="{{route('admin.edit.manager', Crypt::encrypt($analysis->manager_id))}}">{{App\Models\Manager::find($analysis->manager_id)->name}}</a></td>
                                                <td>{{App\Models\BeratingCalculation::find($analysis->berating)->grade}}</td>
                                                <td>{{$analysis->bags}}</td>
                                                <td>{{$analysis->kgs}}</td>
                                                <td>{{$analysis->percentage_analysis}}</td>
                                                <td>{{$analysis->total_in_kg}}kg</td>
                                                <td><a href="{{config('app.url')}}{{$analysis->receipt}}" target=”_blank”><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$analysis->receipt}}" alt="{{$analysis->receipt}}"></a></td>
                                                <td>₦{{number_format($analysis->price, 2)}}</td>
                                                <td>{{$analysis->date}}</td>
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