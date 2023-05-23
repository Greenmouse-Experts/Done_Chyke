<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{config('app.name')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
    <!-- icon cdn -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <!-- fonts cdn -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,800&display=swap" rel="stylesheet" />
    <!-- bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: "Montserrat", sans-serif;
        }

        .login-body {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
        }

        .slide-container {
            width: 50%;
        }

        .login-container {
            width: 50%;
        }

        .login-inside {
            width: 65%;
            padding-left: 80px;
        }

        .logo-class {
            width: 60px;
            margin-bottom: 50px;
        }

        .text-5xl {
            font-size: 40px;
            font-weight: 600;
        }

        .mb-60 {
            margin-bottom: 30px;
        }

        .input-box {
            border: 1px solid gray;
            border-radius: 5px;
            padding: 2px 10px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .input-style {
            border: none;
            outline: none;
            padding: 5px;
            width: 100%;
            /* background-color: #d8d8d8; */
        }

        input[type="checkbox"] {
            accent-color: #d8d8d8 !important;
            width: 18px;
        }

        .btn-style {
            width: 100%;
            border: none;
            font-size: 17px;
            font-weight: 500;
            background-color: black;
            padding: 10px;
            border-radius: 10px;
            color: white;
            margin-top: 10px;
        }

        .forget-style {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0px;
        }

        .link-style {
            text-decoration: none;
            color: #c46a62;
            font-weight: 500;
        }

        .h-full {
            height: 100vh !important;
        }

        .slide-1 {
            background-image: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.8)),
                url("https://res.cloudinary.com/greenmouse-tech/image/upload/v1684774233/don-chyke/Mining-Investment_ztc0kc.webp");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .slide-2 {
            background-image: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.8)),
                url("https://res.cloudinary.com/greenmouse-tech/image/upload/v1684774233/don-chyke/Mining-site_mqj3hc.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .slide-3 {
            background-image: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.8)),
                url("https://res.cloudinary.com/greenmouse-tech/image/upload/v1684774232/don-chyke/logistics_iov42r.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .slide-4 {
            background-image: linear-gradient(to bottom,
                    rgba(0, 0, 0, 0.4),
                    rgba(0, 0, 0, 0.8)),
                url("https://res.cloudinary.com/greenmouse-tech/image/upload/v1684774232/don-chyke/export_kenqa8.jpg");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .slide-content {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            color: white;
            padding-bottom: 50px;
        }

        @media (max-width: 960px) {
            .slide-container {
                display: none;
            }

            .login-container {
                width: 100%;
            }

            .login-inside {
                width: 100%;
                padding: 80px;
            }
        }

        @media (max-width: 460px) {
            .login-inside {
                width: 100%;
                padding: 20px;
            }

            .text-5xl {
                font-size: 35px;
                font-weight: 600;
            }
        }
    </style>
    <script>
        window.setTimeout(function() {
            $(".alert-timeout").fadeTo(500, 0).slideUp(1000, function(){
                $(this).remove(); 
            });
        }, 8000);
    </script>
</head>

<body>
    <!-- Alerts  Start-->
    <div style="z-index: 100000; width: 100%; position: absolute; top: 0">
        @include('layouts.alert')
    </div>
    <!-- Alerts End -->
    <div class="login-body">
        <!-- login screen -->
        <div class="login-container">
            <div class="login-inside">
                <div class="d-lg-flex justify-content-center">
                    <img src="https://res.cloudinary.com/greenmouse-tech/image/upload/v1684768279/don-chyke/WhatsApp_Image_2023-05-22_at_15.21.01_waq42l.jpg" alt="logo" class="logo-class" />
                </div>
                <div>
                    <p class="text-5xl">Login your account</p>
                    <p class="mb-60">Login to stay connected</p>
                </div>
                <div>
                    <form action="{{ route('user.login')}}" method="post">
                    @csrf
                        <div class="input-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z" />
                                <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648Zm-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z" />
                            </svg>
                            <input type="email" placeholder="Email" name="email" class="input-style" />
                        </div>
                        <div class="input-box">
                            <i class="bi bi-file-lock fs-5"></i>
                            <input type="password" placeholder="Password" name="password"  class="input-style" />
                        </div>
                        <div class="forget-style">
                            <div>
                                <input type="checkbox" class="checks" />
                                <label class="">Remember Me</label>
                            </div>
                            <div>
                                <a href="/forgot" class="link-style">Forget Password</a>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn-style">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- carousel information -->
        <div class="slide-container h-full">
            <div id="carouselExampleFade" class="carousel h-full slide carousel-fade">
                <!-- indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="3" aria-label="Slide 4"></button>
                </div>
                <!-- carousel inners -->
                <div class="carousel-inner h-full">
                    <div class="carousel-item active h-full slide-1">
                        <div class="slide-content h-full">
                            <div class="text-center px-5">
                                <p class="text-5xl">Don & Chyke</p>
                                <p>
                                    We are committed to promoting sustainable development in the
                                    mining industry by partnering with local businesses to
                                    provide high-quality minerals to our clients in Nigeria. Our
                                    system is designed to streamline the process of collecting
                                    minerals from local businesses and exporting them to
                                    Nigeria. Contact us today to learn more about how we can
                                    help you with your mineral needs.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item h-full slide-2">
                        <div class="slide-content h-full">
                            <div class="text-center px-5">
                                <p class="text-5xl">Mineral Collection</p>
                                <p>
                                    We work closely with local businesses to ensure that the
                                    minerals they collect meet our quality standards. We have
                                    established a rigorous process for inspecting and testing
                                    the minerals before we accept them for export.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item h-full slide-3">
                        <div class="slide-content h-full">
                            <div class="text-center px-5">
                                <p class="text-5xl">Logistics</p>
                                <p>
                                    We work closely with local businesses to ensure that the
                                    minerals they collect meet our quality standards. We have
                                    established a rigorous process for inspecting and testing
                                    the minerals before we accept them for export.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item h-full slide-4">
                        <div class="slide-content h-full">
                            <div class="text-center px-5">
                                <p class="text-5xl">Export</p>
                                <p>
                                    We have established partnerships with shipping companies and
                                    customs agents to ensure that the minerals are exported to
                                    Nigeria in a timely and efficient manner. We take care of
                                    all the necessary documentation and procedures to ensure
                                    that the minerals are delivered to our clients in Nigeria
                                    without any delays.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
</body>

</html>