@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#"></a></li> -->
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">My Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.upload.profile.picture')}}" method="post" enctype="multipart/form-data" data-toggle="validator">
                            @csrf
                            <div class="form-group">
                                <div class="crm-profile-img-edit position-relative">
                                    @if(Auth::user()->avatar)
                                    <img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{Auth::user()->avatar}}" alt="{{Auth::user()->first_name}}">
                                    @else
                                    <span id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" style="display: flex; justify-content: center; font-size: 2.5rem; background: #c56963; padding: 0.5rem; color: #fff;">
                                        {{ ucfirst(substr(Auth::user()->name, 0, 1)) }} {{ ucfirst(substr(Auth::user()->name, 0, 1)) }}
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
                                {{Auth::user()->account_type}}
                            </div>
                            <div class="form-group">
                                <label for="">Name:</label>
                                {{Auth::user()->name}}
                            </div>
                            <div class="form-group">
                                <label for="">Email:</label>
                                {{Auth::user()->email}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Details</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <form action="{{ route('admin.update.profile')}}" method="post" data-toggle="validator">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="fname">Name:</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{Auth::user()->name}}" placeholder="Name">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="add1">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{Auth::user()->email}}" placeholder="Email Address">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Account</button>
                            </form>
                            <hr>
                            <h5 class="mb-3">Security</h5>
                            <form action="{{ route('admin.update.password')}}" method="post">
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
                                </div>
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection