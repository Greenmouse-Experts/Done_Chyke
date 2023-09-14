@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">All Miscellaneous Expenses</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Miscellaneous Expenses</li>
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
                            <form action="{{ route('admin.expenses')}}" method="POST" data-toggle="validator">
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
                                <th>Miscellaneous Expense Type</th>
                                <th>Supplier</th>
                                <th>Collected By Who</th>
                                <th>Payment Source</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Expense Date</th>
                                <th>Receipt Attachment</th>
                                <th>Recurring Expense</th>
                                <th>Action</th>
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
                                <td>{{$expense->miscellaneous_expense_type}}</td>
                                <td>
                                    @if (App\Models\User::where('id', $expense->supplier)->exists())
                                    {{App\Models\User::find($expense->supplier)->name}}
                                    @else
                                    {{$expense->supplier_additional_field}}
                                    @endif
                                </td>
                                <td>{{$expense->collected_by}}</td>
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
                                <td>
                                    <div class="d-flex align-items-center list-action">
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
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Miscellaneous Expense Type *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter Miscellaneous Expense Type" value="{{$expense->miscellaneous_expense_type}}" name="miscellaneous_expense_type" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Payment Source *</label>
                                                                                <select name="payment_source" class="selectpicker form-control" data-style="py-0" required>
                                                                                    <option value="{{$expense->payment_source}}">{{$expense->payment_source}}</option>
                                                                                    <option value="">-- Select Payment Source --</option>
                                                                                    <option value="Cash">Cash</option>
                                                                                    <option value="Cheque">Cheque</option>
                                                                                </select>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Expense Date *</label>
                                                                                <input type="date" class="form-control" placeholder="Enter date" name="date" value="{{$expense->date}}" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Category *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter category" name="category" value="{{$expense->category}}" required>
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
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label>Supplier *</label>
                                                                                <select name="supplier" id="option" class="selectpicker form-control" data-style="py-0" required>
                                                                                    <option value="{{$expense->supplier}}">
                                                                                        @if (App\Models\User::where('id', $expense->supplier)->exists())
                                                                                        {{App\Models\User::find($expense->supplier)->name}}
                                                                                        @else
                                                                                        {{$expense->supplier_additional_field}}
                                                                                        @endif
                                                                                    </option>
                                                                                    <option value="">-- Select Supplier --</option>
                                                                                    @if(App\Models\User::latest()->where('account_type', '!=', 'Administrator')->where('status', '1')->get()->count() > 0)
                                                                                    @foreach(App\Models\User::latest()->where('account_type', '!=', 'Administrator')->where('status', '1')->get() as $staff)
                                                                                    <option value="{{$staff->id}}">{{$staff->name}}</option>
                                                                                    @endforeach
                                                                                    <option value="0">Others</option>
                                                                                    @else
                                                                                    <option value="">No Supplier Added</option>
                                                                                    @endif
                                                                                </select>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12" style="display: none;" id="textFieldContainer">
                                                                            <div class="form-group">
                                                                                <label for="supplier_additional_field">Other Supplier</label>
                                                                                <input type="text" class="form-control" name="supplier_additional_field" value="{{$expense->supplier_additional_field}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label for="collected_by">Collected By *</label>
                                                                                <input type="text" class="form-control" name="collected_by" value="{{$expense->collected_by}}" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group">
                                                                                <label>Amount *</label>
                                                                                <input type="number" class="form-control" placeholder="Enter amount" name="amount" value="{{$expense->amount}}" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Attach Receipt</label>
                                                                                <div class="custom-file">
                                                                                    <input type="file" class="custom-file-input" id="validatedCustomFile" placeholder="Upload receipt" name="receipt" accept="image/png, image/jpeg, image/jpg">
                                                                                    <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                                                                </div>
                                                                                <span style="font-size: 0.8rem">Attachment longer than 3mb may take longer to upload when saving an expenses</span>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                                                                    <input type="checkbox" class="custom-control-input bg-success" name="recurring_expense" @if($expense->recurring_expense !== null) checked @else @endif>
                                                                                    <label class="custom-control-label" for="customCheck-2">Add as a recurring expense</label>
                                                                                </div>
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
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
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
                                                            <h4 class="mb-3">Are you sure, you want to delete this expense?</h4>
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
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="font-size: 1.1rem; font-weight: 700">Grand Total</td>
                                <td colspan="5" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($expenses->sum('amount'), 2)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const selectElement = document.querySelector("#option");
        const textFieldContainer = document.querySelector("#textFieldContainer");

        selectElement.addEventListener("change", function() {
            if (selectElement.value === "0") {
                textFieldContainer.style.display = "block";
            } else {
                textFieldContainer.style.display = "none";
            }
        });
    });
</script>
@endsection