<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}} | Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{URL::ASSET('assets/images/favicon.png')}}" />
    <link rel="stylesheet" href="{{URL::ASSET('assets/css/backend-plugin.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/css/backende209.css?v=1.0.0')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/%40fortawesome/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{URL::ASSET('assets/vendor/remixicon/fonts/remixicon.css')}}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" integrity="sha512-vebUliqxrVkBy3gucMhClmyQP9On/HAWQdKDXRaAlb/FKuTbxkjPKUyqVOxAcGwFDka79eTF+YXwfke1h3/wfg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/font-awesome-line-awesome/css/all.min.css" integrity="sha512-dC0G5HMA6hLr/E1TM623RN6qK+sL8sz5vB+Uc68J7cBon68bMfKcvbkg6OqlfGHo1nMmcCxO5AinnRTDhWbWsA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        window.setTimeout(function() {
            $(".alert-timeout").fadeTo(500, 0).slideUp(1000, function() {
                $(this).remove();
            });
        }, 10000);
    </script>
</head>

<body class=" ">
    <!-- Alerts  Start-->
    <div style="z-index: 100000; width: 100%; position: absolute; top: 0">
        @include('layouts.alert')
    </div>
    <!-- Alerts End -->

    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Sidebar -->
        @includeIf('layouts.dashboard_sidebar')
        <!-- Sidebar Ends -->

        <!-- Navbar -->
        @includeIf('layouts.dashboard_navbar')
        <!-- Navbar Ends -->

        <!-- Page-Content -->
        @yield('page-content')
        <!-- Page-Content Ends -->

        <!-- Footer -->
        @includeIf('layouts.dashboard_footer')
        <!-- Footer Ends -->
    </div>
    <!-- Wrapper End-->

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

    <!-- DataTable -->
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js" type="text/javascript"></script>

    <script>
        $(document).ready(function() {
            $('.data-table').DataTable({
                dom: "Blfrtip",
                buttons: [{
                        text: 'csv',
                        extend: 'csvHtml5',
                    },
                    {
                        text: 'excel',
                        extend: 'excelHtml5',
                    },
                    {
                        text: 'pdf',
                        extend: 'pdfHtml5',
                    },
                    {
                        text: 'print',
                        extend: 'print',
                    },
                ]
            });

            $('.data-tab').DataTable();
        });
    </script>
  

</body>

</html>