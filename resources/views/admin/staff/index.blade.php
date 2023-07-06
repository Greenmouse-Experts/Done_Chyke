@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Add Staff</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.staff')}}">Staff</a></li>
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
                            <h5 class="card-title">Provide the informations below.</h5>
                            <p class="text-danger">* Indicates required</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.post.staff')}}" method="POST" data-toggle="validator">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Account Type *</label>
                                        <select name="account_type" class="selectpicker form-control" onchange="showDiv(this)" data-style="py-0">
                                            <option value="">-- Select Account Type --</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Accountant">Accountant</option>
                                            <option value="Assistant Manager">Assistant Manager</option>
                                            <option value="Store Personnel">Store Personnel</option>
                                            <option value="Warehouse Personnel">Warehouse Personnel</option>
                                            <option value="Driver">Driver</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name *</label>
                                        <input type="text" class="form-control" placeholder="Enter Name" name="name" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="tel" class="form-control" placeholder="Enter Phone No" name="phone">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" class="form-control" placeholder="Enter Email" name="email" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select name="gender" class="selectpicker form-control" data-style="py-0">
                                            <option value="">-- Select Gender --</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="password" style="display:none;">
                                    <div class="form-group">
                                        <label>Password *</label>
                                        <input type="password" class="form-control" placeholder="Enter Password" name="password">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="comfirm-password" style="display:none;">
                                    <div class="form-group">
                                        <label>Confirm Password *</label>
                                        <input type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="selectpicker form-control" data-style="py-0">
                                            <option value="">-- Select Status --</option>
                                            <option value="true">Active</option>
                                            <option value="false">Inactive</option>
                                        </select>
                                        <span class="text-red">If blank, System will automatically make user <code>Active</code></span>
                                    </div>
                                </div>
                                <div class="col-md-12" id="notify" style="display:none;">
                                    <div class="checkbox d-inline-block mb-3">
                                        <input type="checkbox" name="notify" class="checkbox-input mr-2" id="checkbox1" checked="">
                                        <label for="checkbox1">Notify User by Email</label>
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

<script type="text/javascript">
    function showDiv(select) {
        if (select.value == 'Accountant') {
            document.getElementById('password').style.display = "block";
            document.getElementById('comfirm-password').style.display = "block";
            document.getElementById('notify').style.display = "block";
        } else if (select.value == 'Assistant Manager') {
            document.getElementById('password').style.display = "block";
            document.getElementById('comfirm-password').style.display = "block";
            document.getElementById('notify').style.display = "block";
        } else if (select.value == 'Store Personnel') {
            document.getElementById('password').style.display = "block";
            document.getElementById('comfirm-password').style.display = "block";
            document.getElementById('notify').style.display = "block";
        } else {
            document.getElementById('password').style.display = "none";
            document.getElementById('comfirm-password').style.display = "none";
            document.getElementById('notify').style.display = "none";
        }
    }
</script>
@endsection