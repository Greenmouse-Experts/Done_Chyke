<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
        <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                <i class="ri-menu-line wrapper-menu"></i>
                <a href="{{route('admin.dashboard')}}" class="header-logo">
                    <img src="{{URL::asset('assets/images/logo.png')}}" class="img-fluid rounded-normal" alt="logo">
                    <h5 class="logo-title ml-3">Done & Chyke</h5>

                </a>
            </div>
            <div class="iq-search-bar device-search">
                <span style="font-size: 1.2rem; font-weight: 700;">{{Auth::user()->account_type}} Dashboard</span>
            </div>
            <div class="d-flex align-items-center">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
                    <i class="ri-menu-3-line"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-list align-items-center">
                        <li>
                            <a href="#" class="btn border add-btn shadow-none mx-2 d-none d-md-block" data-toggle="modal" data-target="#fund-account"><i class="las la-plus mr-2"></i>₦{{number_format(App\Models\Wallet::latest()->first()->amount, 2)}}</a>
                        </li>
                        <li class="nav-item nav-icon search-content">
                            <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-search-line"></i>
                            </a>
                            <div class="iq-search-bar iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownSearch">
                                <form action="#" class="searchbox p-2">
                                    <div class="form-group mb-0 position-relative">
                                        <input type="text" class="text search-input font-size-12" placeholder="type here to search...">
                                        <a href="#" class="search-link"><i class="las la-search"></i></a>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                <span class="bg-primary "></span>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 ">
                                        <div class="cust-title p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5 class="mb-0">Notifications</h5>
                                                <a class="badge badge-primary badge-card" href="#">{{App\Models\Notification::latest()->where('to', Auth::user()->id)->where('status', 'Unread')->get()->count()}}</a>
                                            </div>
                                        </div>
                                        @foreach(App\Models\Notification::latest()->where('to', Auth::user()->id)->where('status', 'Unread')->get()->take(5) as $notification)
                                        <div class="px-3 pt-0 pb-0 sub-card">
                                            <a href="{{route('admin.read.notification', Crypt::encrypt($notification->id))}}" class="iq-sub-card">
                                                <div class="media align-items-center cust-card py-3 border-bottom">
                                                    <div class="">
                                                        <img class="avatar-50 rounded-small" src="{{URL::asset('assets/images/logo.png')}}" alt="01">
                                                    </div>
                                                    <div class="media-body ml-3">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-0">{{$notification->title}}</h6>
                                                            <small class="text-dark"><b>{{$notification->created_at->diffForHumans()}}</b></small>
                                                        </div>
                                                        <small class="mb-0">{{$notification->body}}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        @endforeach
                                        <a class="right-ic btn btn-primary btn-block position-relative p-2" href="{{route('admin.notifications')}}" role="button">
                                            View All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown caption-content">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @if(Auth::user()->avatar)
                                    <img class="img-fluid rounded" src="{{Auth::user()->avatar}}" alt="{{Auth::user()->first_name}}">
                                @else
                                <div class="avatar-xs" style="display: inline-block; vertical-align: middle;">
                                    <span class="img-fluid rounded" style="background: #c56963; padding: 0.5rem; color: #fff;">
                                        {{ ucfirst(substr(Auth::user()->name, 0, 1)) }} {{ ucfirst(substr(Auth::user()->name, 1, 1)) }}
                                    </span>
                                </div>
                                @endif
                            </a>
                            
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 text-center">
                                        <div class="media-body profile-detail text-center">
                                            @if(Auth::user()->avatar)
                                            <div style="display: inline-block; vertical-align: middle;">
                                                <img class="rounded img-fluid avatar-70" src="{{Auth::user()->avatar}}" alt="{{Auth::user()->first_name}}">
                                            </div>
                                            @else
                                            <div class="avatar-xs" style="display: inline-block; vertical-align: middle;">
                                                <span class="rounded img-fluid avatar-70" style="background: #c56963; padding: 0.5rem; color: #fff;">
                                                    {{ ucfirst(substr(Auth::user()->name, 0, 1)) }} {{ ucfirst(substr(Auth::user()->name, 1, 1)) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-1">{{Auth::user()->name}}</h5>
                                            <h5 class="mb-1">{{Auth::user()->email}}</h5>
                                            <p class="mb-0">Since {{Auth::user()->created_at->toDayDateTimeString()}}</p>
                                            <div class="d-flex align-items-center justify-content-center mt-3">
                                                <a href="{{route('admin.profile')}}" class="btn border mr-2">Profile</a>
                                                <a data-toggle="modal" data-target="#logout" href="#" class="btn border">Logout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>

<div class="modal fade" id="fund-account" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <h4 class="mb-3">Fund Wallet</h4>
                    <div class="content create-workform bg-body">
                        <form class="paymentForm" data-toggle="validator">
                            @csrf
                            <div class="pb-3">
                                <label class="mb-2">Amount</label>
                                <input type="number" class="form-control" id="amount" placeholder="Enter Amount" required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-lg-12 mt-4">
                                <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                    <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                    <button type="button" onclick="payWithPaystack()" class="btn btn-primary mr-2">Fund</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="logout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-left">
                    <h4 class="mb-3 text-center">Are you sure, you want to logout?</h4>
                    <div class="content create-workform bg-body">
                        <form action="{{route('logout')}}" method="post">
                            @csrf
                            <div class="col-lg-12 mt-4">
                                <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                    <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                    <button type="submit" class="btn btn-primary mr-2">Logout</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var paymentForm = document.querySelector('.paymentForm');
    paymentForm.addEventListener("submit", payWithPaystack, false);

    function payWithPaystack(){
        var handler = PaystackPop.setup({
        key: 'pk_test_dafbbf580555e2e2a10a8d59c6157b328192334d',
        email: '{{Auth::user()->email}}',
        amount: document.getElementById("amount").value * 100,
        ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
        callback: function(response){
            // alert(JSON.stringify(response))
            let url = '{{ route("admin.account.funding.confirm", [":response", ":amount"]) }}';
            url = url.replace(':response', response.reference);
            url = url.replace(':amount', document.getElementById("amount").value);
            document.location.href=url;
        },
        onClose: function(){
            alert('window closed');
        }
        });
    handler.openIframe();
  }
</script>