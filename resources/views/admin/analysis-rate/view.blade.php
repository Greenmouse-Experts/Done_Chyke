@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Analysis Rates List</h4>
                        <p class="mb-0">All analysis rates in one place </p>
                    </div>
                    <a href="{{route('admin.add.rate.analysis')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Percentage Min (%)</th>
                                <th>Percentage Max (%)</th>
                                <th>Dollar Rate</th>
                                <th>Exchange Rate</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\AnalysisCalculation::latest()->get() as $berating)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$berating->percentage_min}}</td>
                                <td>{{$berating->percentage_max}}</td>
                                <td>{{$berating->dollar_rate}}</td>
                                <td>{{$berating->exchange_rate}}</td>
                                <td>
                                    @if($berating->status == 'Active')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$berating->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <span data-toggle="modal" data-target="#edit-{{$berating->id}}">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="#"><i class="ri-pencil-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="edit-{{$berating->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                <form action="{{route('admin.update.rate.analysis', Crypt::encrypt($berating->id))}}" method="POST" data-toggle="validator">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Percentage Min(%) *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter percentage minimum" value="{{$berating->percentage_min}}" name="percentage_min" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Percentage(%) *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter percentage maximum" value="{{$berating->percentage_max}}" name="percentage_max" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Percentage Max(%) *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter Dollar Rate" value="{{$berating->dollar_rate}}" name="dollar" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Exchange Rate *</label>
                                                                                <input type="number" class="form-control" placeholder="Enter Exchange Rate" value="{{$berating->exchange_rate}}" name="exchange" required>
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
                                        @if($berating->status == 'Active')
                                        <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Deactivate"href="{{route('admin.deactivate.rate.analysis', Crypt::encrypt($berating->id))}}"><i class="ri-stop-circle-line mr-0"></i></a>
                                        @else
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Active" href="{{route('admin.activate.rate.analysis', Crypt::encrypt($berating->id))}}"><i class="ri-play-line mr-0"></i></a>
                                        @endif
                                        <span data-toggle="modal" data-target="#delete-{{$berating->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$berating->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <h4 class="mb-3">Are you sure, you want to delete this analysis price?</h4>
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.delete.rate.analysis', Crypt::encrypt($berating->id))}}" method="post">
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
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection