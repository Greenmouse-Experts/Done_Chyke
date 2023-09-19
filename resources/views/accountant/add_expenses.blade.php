@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Add Miscellaneous Expense</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('expenses.view')}}">Miscellaneous Expense</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add</li>
                </ol>
            </nav>
        </div>
    </div>
     <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Provide the following informations</h5>
                            <p class="text-danger"> * Indicates required</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('expenses.post')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Miscellaneous Expense Type *</label>
                                        <input type="text" class="form-control" placeholder="Enter Miscellaneous Expense Type" name="miscellaneous_expense_type" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Source *</label>
                                        <select name="payment_source" class="selectpicker form-control" data-style="py-0" required>
                                            <option value="">-- Select Payment Source --</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer by Cheques">Transfer by Cheques</option>
                                            <option value="Direct Transfer">Direct Transfer</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expense Date *</label>
                                        <input type="date" class="form-control" placeholder="Enter date" name="date" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category *</label>
                                        <input type="text" class="form-control" placeholder="Enter category" name="category" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description *</label>
                                        <textarea name="description" class="form-control" placeholder="Enter description" required></textarea> 
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Collected from *</label>
                                        <select name="supplier" id="option" class="selectpicker form-control" data-style="py-0" required>
                                            <option value="">-- Select Collected from --</option>
                                            @if(App\Models\User::latest()->where('account_type', '!=', 'Administrator')->where('status', '1')->get()->count() > 0)
                                                @foreach(App\Models\User::latest()->where('account_type', '!=', 'Administrator')->where('status', '1')->get() as $staff)
                                                <option value="{{$staff->id}}">{{$staff->name}}</option>
                                                @endforeach
                                                <option value="0">Others</option>
                                            @else
                                            <option value="">None Added</option>
                                            @endif
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount *</label>
                                        <input type="number" class="form-control" placeholder="Enter amount" name="amount" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-12" style="display: none;" id="textFieldContainer">
                                    <div class="form-group">
                                        <label for="supplier_additional_field">Other Supplier</label>
                                        <input type="text" class="form-control" name="supplier_additional_field">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="collected_by">Collected By *</label>
                                        <input type="text" class="form-control" name="collected_by" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach Receipt</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="validatedCustomFile" placeholder="Upload receipt" name="receipt" accept="image/png, image/jpeg, image/jpg">
                                            <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                                        </div>
                                        <span style="font-size: 0.9rem">Attachment longer than 3mb may take longer to upload when saving an expenses</span>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                                            <input type="checkbox" class="custom-control-input bg-success" id="customCheck-2" name="recurring_expense">
                                            <label class="custom-control-label" for="customCheck-2">Add as a recurring expense</label>
                                        </div>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </form>
                    </div>
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