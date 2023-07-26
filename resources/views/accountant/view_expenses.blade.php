@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Expenses</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div style="justify-content: flex-start;">
                            <form action="{{ route('expenses.view')}}" method="POST" data-toggle="validator">
                                @csrf
                                <label class="mr-2"><strong>Start Date :</strong>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                                </label>&nbsp;&nbsp;
                                <label class="mr-2"><strong>End Date :</strong>
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                                </label>
                                <label>
                                    <select class="form-control" name="source">
                                        <option value="{{$source ?? null}}">{{$source ?? '- Select Payment Source -'}}</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                            <a href="{{route('expenses.add')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Expense</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Supplier</th>
                                <th>Payment Source</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th>Receipt Attachment</th>
                                <th>Recurring Expense</th>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($expenses as $expense)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    @if (App\Models\User::where('id', $expense->supplier)->exists())
                                    {{App\Models\User::find($expense->supplier)->name}}
                                    @else
                                    <b>{{ 'USER DELETED' }}</b>
                                    @endif
                                </td>
                                <td>{{$expense->payment_source}}</td>
                                <td>{{$expense->category}}</td>
                                <td>{{$expense->description}}</td>
                                <td>₦{{number_format($expense->amount, 2)}}</td>
                                <td>{{$expense->date}}</td>
                                <td>
                                    @if($expense->receipt == null)
                                    <p>None</p>
                                    @else
                                    <span data-toggle="modal" data-target="#preview-{{$expense->id}}">
                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Preview Receipt Attachment" data-original-title="Preview Receipt Attachment"><img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$expense->receipt}}" alt="{{$expense->receipt}}"></a>
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($expense->recurring_expense == null)
                                    <span class="badge badge-danger">No</span>
                                    @else
                                    <span class="badge badge-success">Yes</span>
                                    @endif
                                </td>
                                <div class="modal fade" id="preview-{{$expense->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Prview Receipt Attachment</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <img src="{{$expense->receipt}}" alt="{{$expense->receipt}}" class="img-fluid rounded">
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
                                <td colspan="5" style="font-size: 1.1rem; font-weight: 700">Grand Total</td>
                                <td colspan="4" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($expenses->sum('amount'), 2)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection