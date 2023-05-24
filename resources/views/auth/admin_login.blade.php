@extends('layouts.frontend')

@section('page-content')
    <div class="wrapper">
        <section class="login-content">
            <div class="container">
                <div class="row align-items-center justify-content-center height-self-center">
                    <div class="col-lg-6">
                        <div class="card auth-card" style="border: 1px solid #f4f5fa !important;">
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center auth-content">
                                    <div class="col-lg-12 align-self-center">
                                        <div class="col-12 text-center">
                                            <!-- <img src="{{URL::asset('assets/images/full_logo.jpeg')}}" alt="" > -->
                                            <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1684768279/don-chyke/WhatsApp_Image_2023-05-22_at_15.21.01_waq42l.jpg" alt="logo" width="80" />
                                        </div>
                                        <div class="p-3">
                                            <h2 class="mb-4 text-center">Admin Login</h2>
                                            <form action="{{ route('post.admin.login')}}" method="post">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="floating-label form-group">
                                                            <input class="floating-input form-control" type="email" name="email" placeholder=" ">
                                                            <label>Email</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="floating-label form-group">
                                                            <input class="floating-input form-control" type="password" name="password" placeholder=" ">
                                                            <label>Password</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="custom-control custom-checkbox mb-3">
                                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                                            <label class="custom-control-label control-label-1" for="customCheck1">Remember Me</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="form-btn btn btn-primary" style="width: 100%">Login</button>
                                            </form>
                                        </div>
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