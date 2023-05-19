@extends('layouts.frontend')

@section('page-content')
<div class="wrapper">
    <section class="login-content">
        <div class="container">
            <div class="row align-items-center justify-content-center height-self-center">
                <div class="col-lg-8">
                    <div class="card auth-card">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center auth-content">
                                <div class="col-lg-7 align-self-center">
                                    <div class="p-3">
                                        @if($user->avatar)
                                        <img id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" src="{{$user->avatar}}" alt="{{$user->first_name}}">
                                        @else
                                        <span id="file-ip-1-preview" class="rm-profile-pic rounded avatar-100" style="display: flex; justify-content: center; font-size: 2.5rem; background: #c56963; padding: 0.5rem; color: #fff;">
                                            {{ ucfirst(substr($user->name, 0, 1)) }} {{ ucfirst(substr($user->name, 1, 1)) }}
                                        </span>
                                        @endif
                                        <h2 class="mb-2">Hi {{$user->name}},</h2>
                                        <p>Enter your password to access the admin.</p>
                                        <form action="{{ route('update.new.password', Crypt::encrypt($user->id))}}" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="floating-label form-group">
                                                        <input class="floating-input form-control" type="text" name="code" placeholder=" ">
                                                        <label>Code</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="floating-label form-group">
                                                        <input class="floating-input form-control" type="password" name="new_password" placeholder=" ">
                                                        <label>Password</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="floating-label form-group">
                                                        <input class="floating-input form-control" type="password" name="new_password_confirmation" placeholder=" ">
                                                        <label>Password</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary" style="width: 100%">Reset Password</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-5 content-right">
                                    <img src="{{URL::ASSET('assets/images/login/01.png')}}" class="img-fluid image-right" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection