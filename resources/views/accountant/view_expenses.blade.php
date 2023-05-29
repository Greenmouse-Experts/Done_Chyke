@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Expenses</h4>
                        <p class="mb-0">All your expenses list in one place</p>
                    </div>
                    <a href="{{route('expenses.add')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Expense</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\Expenses::latest()->where('user_id', Auth::user()->id)->get() as $expense)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$expense->title}}</td>
                                <td>{{$expense->description}}</td>
                                <td>₦{{number_format($expense->amount, 2)}}</td>
                                <td>{{$expense->date}}</td>
                                <td>
                                    @if($expense->receipt == null)
                                    <p>None</p>
                                    @else
                                    <a href="{{config('app.url')}}{{$expense->receipt}}" target=”_blank”>
                                        <img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$expense->receipt}}" alt="{{$expense->receipt}}">
                                    </a>
                                    @endif
                                </td>
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