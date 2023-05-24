@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Tin Payment Analysis</h4>
                        <p class="mb-0">All tin payment analysis in one place </p>
                    </div>
                    <a href="{{route('payment.analysis.tin.add', 0)}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>

            <div class="col-lg-12">
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
                                <th>Percentage (%) Analysis</th>
                                <th>Receipt</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\TinPaymentAnalysis::latest()->where('user_id', Auth::user()->id)->get() as $analysis)
                            <tr>
                                <td>
                                {{$loop->iteration}}
                                </td>
                                <td>@if($analysis->type == 'kg')TIN (KG) @else TIN (POUND) @endif</td>
                                <td>{{$analysis->customer}}</td>
                                <td>{{App\Models\Manager::find($analysis->manager_id)->name}}</td>
                                <td>{{App\Models\BeratingCalculation::find($analysis->berating)->grade}}</td>
                                <td>{{$analysis->bags}}</td>
                                <td>{{$analysis->pounds}}</td>
                                <td>{{$analysis->percentage_analysis}}</td>
                                <td><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$analysis->receipt}}" alt="{{$analysis->receipt}}"></td>
                                <td>₦{{number_format($analysis->price, 2)}}</td>
                                <td>{{$analysis->date}}</td>
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