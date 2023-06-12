@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">All Staff</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Staff</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                    <a href="{{route('admin.add.staff')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Staff</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Photo</th>
                                <th>Account Type</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Phone Number</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\User::latest()->where('account_type', '!=', 'Administrator')->get() as $staff)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                @if($staff->avatar)
                                    <img class="rounded" width="50" src="{{$staff->avatar}}" alt="{{$staff->first_name}}">
                                @else
                                <div class="avatar-xs" style="display: inline-block; vertical-align: middle;">
                                    <span class="img-fluid rounded" style="background: #c56963; padding: 0.5rem; color: #fff;">
                                        {{ ucfirst(substr($staff->name, 0, 1)) }} {{ ucfirst(substr($staff->name, 1, 1)) }}
                                    </span>
                                </div>
                                @endif
                                </td>
                                <td>{{$staff->account_type}}</td>
                                <td>{{$staff->name}}</td>
                                <td>{{$staff->email}}</td>
                                <td>
                                    <span data-toggle="tooltip">
                                        <input type="text" class="form-control" value="{{$staff->current_password}}" id="passwordShow" disabled style="width: auto; border: none; outline: none; background-color: #fff !important;">
                                    </span>
                                </td>
                                <td>{{$staff->phone}}</td>
                                <td>{{$staff->gender}}</td>
                                <td>
                                    @if($staff->status == '1')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$staff->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="View/Edit" data-original-title="View/Edit" href="{{route('admin.edit.staff', Crypt::encrypt($staff->id))}}"><i class="ri-eye-line mr-0"></i></a>
                                        @if($staff->status == '1')
                                        <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Deactivate" data-original-title="Deactivate"href="{{route('admin.deactivate.staff', Crypt::encrypt($staff->id))}}"><i class="ri-stop-circle-line mr-0"></i></a>
                                        @else
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Active" data-original-title="Active" href="{{route('admin.activate.staff', Crypt::encrypt($staff->id))}}"><i class="ri-play-line mr-0"></i></a>
                                        @endif
                                        <span data-toggle="modal" data-target="#delete-{{$staff->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$staff->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                <form action="{{route('admin.delete.staff', Crypt::encrypt($staff->id))}}" method="post">
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

<script>
    function showPassword() {
        var x = document.getElementById("passwordShow");
        x.type = "text";
    }

    function hidePassword() {
        var x = document.getElementById("passwordShow");            
        x.type = "password";
    }
</script>
@endsection