@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Tin Material List</h4>
                        <p class="mb-0">All tin materials in one place </p>
                    </div>
                    <a href="{{route('admin.add.material.tin')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Grade</th>
                                <th>Materials</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\TinMaterial::latest()->get() as $material)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$material->grade}}</td>
                                <td>{{$material->material}}</td>
                                <td>{{$material->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <span data-toggle="modal" data-target="#edit-{{$material->id}}">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="#"><i class="ri-pencil-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="edit-{{$material->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                <form action="{{route('admin.update.material.tin', Crypt::encrypt($material->id))}}" method="POST" data-toggle="validator">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Grade *</label>
                                                                                <input type="text" class="form-control" placeholder="Enter grade" value="{{$material->grade}}" name="grade" required>
                                                                                <div class="help-block with-errors"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Material *</label>
                                                                                <input type="number" class="form-control" placeholder="Enter material" value="{{$material->material}}" name="material" required>
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
                                        <span data-toggle="modal" data-target="#delete-{{$material->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$material->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <h4 class="mb-3">Are you sure, you want to delete this tin material?</h4>
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.delete.material.tin', Crypt::encrypt($material->id))}}" method="post">
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