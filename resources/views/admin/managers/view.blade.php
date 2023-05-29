@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Managers List</h4>
                        <p class="mb-0">All your Managers List in one Place </p>
                    </div>
                    <a href="{{route('admin.add.manager')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Manager</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\Manager::latest()->get() as $manager)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$manager->name}}</td>
                                <td>{{$manager->email}}</td>
                                <td>{{$manager->phone}}</td>
                                <td>{{$manager->gender}}</td>
                                <td>
                                    @if($manager->status == '1')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$manager->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit" href="{{route('admin.edit.manager', Crypt::encrypt($manager->id))}}"><i class="ri-pencil-line mr-0"></i></a>
                                        @if($manager->status == '1')
                                        <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Deactivate" data-original-title="Deactivate"href="{{route('admin.deactivate.manager', Crypt::encrypt($manager->id))}}"><i class="ri-stop-circle-line mr-0"></i></a>
                                        @else
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Active" data-original-title="Active" href="{{route('admin.activate.manager', Crypt::encrypt($manager->id))}}"><i class="ri-play-line mr-0"></i></a>
                                        @endif
                                        <span data-toggle="modal" data-target="#delete-{{$manager->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$manager->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <h4 class="mb-3">Are you sure, you want to delete this user?</h4>
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.delete.manager', Crypt::encrypt($manager->id))}}" method="post">
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