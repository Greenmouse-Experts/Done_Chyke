@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Columbite Payment Analysis</h4>
                        <p class="mb-0">All columbite payment analysis in one place </p>
                    </div>
                    <a href="{{route('payment.analysis.columbite.add', 'pound')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-pound-tab" data-toggle="pill" href="#pills-pound" role="tab" aria-controls="pills-pound" aria-selected="true">POUND</a>
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
                                                <th>Customer Name</th>
                                                <th>Manager</th>
                                                <th>Berating</th>
                                                <th>Bags</th>
                                                <th>Pounds</th>
                                                <th>Total In Pounds</th>
                                                <th>Receipt</th>
                                                <th>Total Price</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach(App\Models\ColumbitePaymentAnalysis::latest()->where('user_id', Auth::user()->id)->where('type', 'pound')->get() as $analysis)
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                <td>@if($analysis->type == 'kg')Columbite (KG) @else Columbite (POUND) @endif</td>
                                                <td>{{$analysis->customer}}</td>
                                                <td>{{App\Models\Manager::find($analysis->manager_id)->name}}</td>
                                                <td>{{App\Models\BeratingCalculation::find($analysis->berating)->grade}}</td>
                                                <td>{{$analysis->bags}}</td>
                                                <td>{{$analysis->pounds}}</td>
                                                <td>{{$analysis->total_in_pounds}}lbs</td>
                                                <td><a href="{{config('app.url')}}{{$analysis->receipt}}" target=”_blank”><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$analysis->receipt}}" alt="{{$analysis->receipt}}"></a></td>
                                                <td>₦{{number_format($analysis->price, 2)}}</td>
                                                <td>{{$analysis->date}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-kg" role="tabpanel" aria-labelledby="pills-kg-tab">
                              
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