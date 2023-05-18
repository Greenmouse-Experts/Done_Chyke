@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
     <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add Assistant Manager</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.post.manager.assistance')}}" method="POST" data-toggle="validator">
                            @csrf
                            <div class="row">
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
                                <div class="col-md-12">
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
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password *</label>
                                        <input type="password" class="form-control" placeholder="Enter Password" name="password" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm Password *</label>
                                        <input type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation" required>
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
                                <div class="col-md-12">
                                    <div class="checkbox d-inline-block mb-3">
                                        <input type="checkbox" name="notify" class="checkbox-input mr-2" id="checkbox1" checked="">
                                        <label for="checkbox1">Notify User by Email</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
      </div>
@endsection