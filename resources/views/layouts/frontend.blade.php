<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}} | Login</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{URL::ASSET('assets/images/favicon.png')}}" />
    <link rel="stylesheet" href="{{URL::ASSET('assets/css/backend-plugin.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/css/backende209.css?v=1.0.0')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/%40fortawesome/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/remixicon/fonts/remixicon.css')}}">
    <script>
        window.setTimeout(function() {
            $(".alert-timeout").fadeTo(500, 0).slideUp(1000, function(){
                $(this).remove(); 
            });
        }, 8000);
    </script>
</head>

<body class=" ">

    <!-- Alerts  Start-->
    <div style="z-index: 100000; width: 100%; position: absolute; top: 0">
        @include('layouts.alert')
    </div>
    <!-- Alerts End -->

    <!-- Page-Content -->
    @yield('page-content')
    <!-- Page-Content Ends -->

    <!-- Backend Bundle JavaScript -->
    <script src="{{URL::ASSET('assets/js/backend-bundle.min.js')}}"></script>

    <!-- Table Treeview JavaScript -->
    <script src="{{URL::ASSET('assets/js/table-treeview.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{URL::ASSET('assets/js/customizer.js')}}"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="{{URL::ASSET('assets/js/chart-custom.js')}}"></script>

    <!-- app JavaScript -->
    <script src="{{URL::ASSET('assets/js/app.js')}}"></script>

    <script>
        function showPreview(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("file-ip-1-preview");
                preview.src = src;
                // preview.style.display = "block";
            }
        }
    </script>
</body>

</html>