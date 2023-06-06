@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Edit Staff</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.staff')}}">Staff</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{$user->name}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.update.staff.profile.picture', Crypt::encrypt($user->id))}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="crm-profile-img-edit position-relative">
                                    @if($user->avatar)
                                    <img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$user->avatar}}" alt="{{$user->first_name}}">
                                    @else
                                    <span id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" style="display: flex; justify-content: center; font-size: 2.5rem; background: #c56963; padding: 0.5rem; color: #fff;">
                                        {{ ucfirst(substr($user->name, 0, 1)) }} {{ ucfirst(substr($user->name, 1, 1)) }}
                                    </span>
                                    @endif
                                   <div class="crm-p-image bg-primary">
                                        <i class="las la-pen upload-button"></i>
                                        <input class="file-upload" type="file" name="avatar" onchange="showPreview(event);" accept="image/*">
                                    </div>
                                </div>
                                <div class="img-extension mt-3">
                                    <div class="d-inline-block align-items-center">
                                        <span>Only</span>
                                        <a href="javascript:void();">.jpg</a>
                                        <a href="javascript:void();">.png</a>
                                        <a href="javascript:void();">.jpeg</a>
                                        <span>allowed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                            <button type="submit" class="btn btn-primary">Upload Profile Picture</button>
                            </div>
                            <div class="form-group">
                                <label>Role:</label>
                                {{$user->account_type}}
                            </div>
                            <div class="form-group">
                                <label for="">Name:</label>
                                {{$user->name}}
                            </div>
                            <div class="form-group">
                                <label for="">Email:</label>
                                {{$user->email}}
                            </div>
                            <div class="form-group">
                                <label for="">Gender:</label>
                                {{$user->gender}}
                            </div>
                            <div class="form-group">
                                <label for="">Status:</label>
                                @if($user->status == '1')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="">Date Created:</label>
                                {{$user->created_at->toDayDateTimeString()}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Edit User</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <form action="{{ route('admin.update.staff.profile', Crypt::encrypt($user->id))}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name *</label>
                                            <input type="text" class="form-control" placeholder="Enter Name" value="{{$user->name}}"name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="tel" class="form-control" placeholder="Enter Phone No" value="{{$user->phone}}" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" placeholder="Enter Email" value="{{$user->email}}" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="selectpicker form-control" data-style="py-0">
                                                <option value="{{$user->gender}}">{{$user->gender}}</option>
                                                <option value="">-- Select Gender --</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update {{$user->name}} Account</button>
                            </form>
                            @if($user->account_type == 'Accountant' || $user->account_type == 'Assistant Manager')
                            <hr>
                            <h5 class="mb-3">Security</h5>
                            <form action="{{ route('admin.update.staff.password', Crypt::encrypt($user->id))}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="pass">Password:</label>
                                        <input type="password" class="form-control" id="pass" name="new_password" placeholder="Password">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="rpass">Repeat Password:</label>
                                        <input type="password" class="form-control" id="rpass" name="new_password_confirmation" placeholder="Repeat Password">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="checkbox d-inline-block mb-3">
                                            <input type="checkbox" name="notify" class="checkbox-input mr-2" id="checkbox1" checked="">
                                            <label for="checkbox1">Notify User by Email</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Change {{$user->name}} Password</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection