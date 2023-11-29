@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Add Analysis Rates List</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('rates.analysis')}}">Analysis Rate List</a></li>
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
                            <h4 class="card-title">Add Analysis Rate</h4>
                            <p class="text-danger">* Indicates required</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{route('post.rate.analysis')}}" method="POST" data-toggle="validator">
                        @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Percentage Min(%) *</label>
                                        <input type="text" class="form-control" placeholder="Enter percentage minimum" name="percentage_min" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Percentage Max(%) *</label>
                                        <input type="text" class="form-control" placeholder="Enter percentage maximum" name="percentage_max" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Dollar Rate *</label>
                                        <input type="text" class="form-control" placeholder="Enter Dollar Rate" name="dollar" required>
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
@endsection