@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add Analysis Calculation</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.post.calculation.analysis')}}" method="POST" data-toggle="validator">
                        @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Percentage(%) *</label>
                                        <input type="text" class="form-control" placeholder="Enter Percentage" name="percentage" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Dollar Rate *</label>
                                        <input type="number" class="form-control" placeholder="Enter Dollar Rate" name="dollar" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Exchange Rate *</label>
                                        <input type="number" class="form-control" placeholder="Enter Exchange Rate" name="exchange" required>
                                        <div class="help-block with-errors"></div>
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