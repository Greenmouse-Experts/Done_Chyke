@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#"></a></li> -->
                    <li class="breadcrumb-item active" aria-current="page">Expenses</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">All Expenses</h4>
                        <p class="mb-0">All expenses list in one place</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Accountant</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Receipt</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($expenses as $expense)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>
                                <td>
                                    @if (App\Models\User::where('id', $expense->user_id)->exists())
                                    <p>{{App\Models\User::find($expense->user_id)->account_type}}</p>
                                    {{App\Models\User::find($expense->user_id)->name}}
                                    @else
                                    <b>{{ 'USER DELETED' }}</b>
                                    @endif
                                </td>
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
                                <td>
                                    <span data-toggle="modal" data-target="#edit-{{$expense->id}}">
                                        <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="#"><i class="ri-pencil-line mr-0"></i></a>
                                    </span>
                                    <div class="modal fade" id="edit-{{$expense->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <form action="{{route('admin.expense.update', Crypt::encrypt($expense->id))}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Title *</label>
                                                                            <input type="text" class="form-control" placeholder="Enter title" value="{{$expense->title}}" name="title" required>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Description *</label>
                                                                            <textarea name="description" class="form-control" placeholder="Enter description" value="{{$expense->description}}" required>{{$expense->description}}</textarea> 
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Amount *</label>
                                                                            <input type="number" class="form-control" placeholder="Enter amount" name="amount" value="{{$expense->amount}}"required>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Date</label>
                                                                            <input type="date" class="form-control" placeholder="Enter date" name="date" value="{{$expense->date}}" required>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label>Receipt</label>
                                                                            <input type="file" class="form-control" placeholder="Upload receipt" name="receipt" accept="image/png, image/jpeg, image/jpg">
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
                                    <span data-toggle="modal" data-target="#delete-{{$expense->id}}">
                                        <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="{{route('admin.expense.delete', Crypt::encrypt($expense->id))}}"><i class="ri-delete-bin-line mr-0"></i></a>
                                    </span>
                                    <div class="modal fade" id="delete-{{$expense->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                        <h4 class="mb-3">Are you sure, you want to delete this expenses?</h4>
                                                        <p><span class="text-danger">Note:</span> Every details attached to this expenses will be deleted and amount will be deposited back to the WALLET.</p>
                                                        <div class="content create-workform bg-body">
                                                            <form action="{{route('admin.expense.delete', Crypt::encrypt($expense->id))}}" method="post">
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
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection