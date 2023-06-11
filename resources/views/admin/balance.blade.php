@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">All Balances</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">balances</li>
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
                            <form action="{{ route('admin.daily.balance')}}" method="POST" data-toggle="validator">
                                @csrf
                                <label class="mr-2"><strong>Start Date :</strong>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}" required>
                                </label>&nbsp;&nbsp;
                                <label class="mr-2"><strong>End Date :</strong>
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}" required>
                                </label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                        <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                            <a class="text-white btn btn-primary add-list">{{\Carbon\Carbon::now()->toFormattedDateString()}} Starting Balance - <span style="font-size: 1.5rem;">₦{{number_format($totalStartingBalance, 2)}}</span></a>
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
                                <th>Date</th>
                                <th>Starting Balance</th>
                                <th>Additional Income</th>
                                <th>Amount Used</th>
                                <th>Remaining Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($balances as $balance)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>
                                <td>{{$balance->date}}</td>
                                <td>₦{{number_format($balance->starting_balance, 2)}}</td>
                                <td>₦{{number_format($balance->additional_income, 2)}}</td>
                                <td>₦{{number_format($balance->amount_used, 2)}}</td>
                                <td>₦{{number_format($balance->remaining_balance, 2)}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <span data-toggle="modal" data-target="#edit-{{$balance->id}}">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="#"><i class="ri-pencil-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="edit-{{$balance->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="popup text-left">
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.daily.balance.update', Crypt::encrypt($balance->id))}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Starting Balance*</label>
                                                                                <input type="number" class="form-control" placeholder="Enter startng balance" name="starting_balance" value="{{$balance->starting_balance}}" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Additional Income</label>
                                                                                <input type="number" class="form-control" placeholder="Enter additional income" value="{{$balance->additional_income}}" name="additional_income">
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Amount Used</label>
                                                                                <input type="number" class="form-control" placeholder="Enter amount used" value="{{$balance->amount_used}}" name="amount_used">
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mt-5">
                                                                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                                                                        <button type="reset" class="btn btn-danger">Reset</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span data-toggle="modal" data-target="#delete-{{$balance->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$balance->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <h4 class="mb-3">Are you sure, you want to delete this balance?</h4>
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.daily.balance.delete', Crypt::encrypt($balance->id))}}" method="post">
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
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="font-size: 1.1rem; font-weight: 700">Total Remaining Balance</td>
                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('remaining_balance'), 2)}}</td>
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