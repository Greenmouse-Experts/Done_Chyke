@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">All Administrator</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">admin</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                    <button data-toggle="modal" data-target="#add" class="btn btn-primary text-white add-list"><i class="las la-plus mr-3"></i>Add Admin</button>
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
                                <th>Role</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\User::latest()->where('account_type', '=', 'Administrator')->get() as $admin)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                    @if($admin->avatar)
                                    <img class="rounded" width="50" src="{{$admin->avatar}}" alt="{{$admin->first_name}}">
                                    @else
                                    <div class="avatar-xs" style="display: inline-block; vertical-align: middle;">
                                        <span class="img-fluid rounded" style="background: #c56963; padding: 0.5rem; color: #fff;">
                                            {{ ucfirst(substr($admin->name, 0, 1)) }} {{ ucfirst(substr($admin->name, 1, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                </td>
                                <td>{{$admin->account_type}}</td>
                                <td>{{$admin->name}}</td>
                                <td>{{$admin->email}}</td>
                                <td>
                                    <span data-toggle="tooltip" data-placement="top" title="Password" data-original-title="Password">
                                        <input type="text" class="form-control" value="{{$admin->current_password}}" id="passwordShow" disabled style="width: auto; border: none; outline: none; background-color: #fff !important;">
                                    </span>
                                </td>
                                <td>{{$admin->phone}}</td>
                                <td>{{$admin->gender}}</td>
                                <td>
                                    @if($admin->role == 'Master')
                                    <span class="badge bg-success">{{$admin->role}}</span>
                                    @else
                                    <span class="badge bg-danger">{{$admin->role}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($admin->status == '1')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$admin->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        @if($admin->role !== 'Master')
                                        <span data-toggle="modal" data-target="#edit-{{$admin->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit" href="#"><i class="ri-eye-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="edit-{{$admin->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                            <form action="{{route('admin.update.sub.admin', Crypt::encrypt($admin->id))}}" method="POST" data-toggle="validator">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Name *</label>
                                                                            <input type="text" class="form-control" placeholder="Enter Name" name="name" value="{{$admin->name}}" required>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Phone Number</label>
                                                                            <input type="tel" class="form-control" placeholder="Enter Phone No" value="{{$admin->phone}}" name="phone">
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Email *</label>
                                                                            <input type="email" class="form-control" placeholder="Enter Email" value="{{$admin->email}}"  name="email" required>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Gender</label>
                                                                            <select name="gender" class="selectpicker form-control" data-style="py-0">
                                                                                <option value="{{$admin->gender}}">{{$admin->gender}}</option>
                                                                                <option value="">-- Select Gender --</option>
                                                                                <option value="Male">Male</option>
                                                                                <option value="Female">Female</option>
                                                                            </select>
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Password *</label>
                                                                            <input type="password" class="form-control" placeholder="Enter Password" name="password">
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Confirm Password *</label>
                                                                            <input type="password" class="form-control" placeholder="Enter Confirm Password" name="password_confirmation">
                                                                            <div class="help-block with-errors"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="checkbox d-inline-block mb-3">
                                                                            <input type="checkbox" name="notify" class="checkbox-input mr-2" id="checkbox1" checked="">
                                                                            <label for="checkbox1">Notify User by Email</label>
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
                                        @if($admin->status == '1')
                                        <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Deactivate" data-original-title="Deactivate" href="{{route('admin.deactivate.sub.admin', Crypt::encrypt($admin->id))}}"><i class="ri-stop-circle-line mr-0"></i></a>
                                        @else
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Active" data-original-title="Active" href="{{route('admin.activate.sub.admin', Crypt::encrypt($admin->id))}}"><i class="ri-play-line mr-0"></i></a>
                                        @endif

                                        <span data-toggle="modal" data-target="#delete-{{$admin->id}}">
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        </span>
                                        <div class="modal fade" id="delete-{{$admin->id}}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                                <form action="{{route('admin.delete.sub.admin', Crypt::encrypt($admin->id))}}" method="post">
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
                                        @endif
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

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="popup text-left">
                    <form action="{{route('admin.add.sub.admin')}}" method="POST" data-toggle="validator">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" class="form-control" placeholder="Enter Email" name="email" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" class="form-control" placeholder="Enter Password" name="password">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                            <div class="col-md-12">
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