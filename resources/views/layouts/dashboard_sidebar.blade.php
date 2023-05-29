<div class="iq-sidebar sidebar-default ">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{route('dashboard')}}" class="header-logo">
            <img src="{{URL::asset('assets/images/logo-white.jpg')}}" class="img-fluid rounded-normal light-logo" alt="logo">
            <h5 class="logo-title light-logo ml-2 text-white">{{config('app.name')}}</h5>
        </a>
        <div class="iq-menu-bt-sidebar ml-0">
            <i class="las la-bars wrapper-menu"></i>
        </div>
    </div>
    <div class="data-scrollbar" data-scroll="1">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                <li class="{{ (request()->is('dashboard')) ? 'active' : '' }}">
                    <a href="{{route('dashboard')}}" class="svg-icon">
                        <svg class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->account_type == 'Assistant Manager')
                <li class="{{ (request()->is('assistant-manager/payment/analysis*')) ? 'active' : '' }}">
                    <a href="#analysis" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-calculator-line mr-0"></i>
                        <span class="ml-4">Payment Analysis</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('assistant-manager/payment/analysis/tin/view')) ? 'active' : '' }}">
                            <a href="{{route('payment.analysis.tin.view')}}">
                                <i class="las la-minus"></i><span>Tin</span>
                            </a>
                            <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('assistant-manager/payment/analysis/tin/add')) ? 'active' : '' }}">
                                    <a href="{{route('payment.analysis.tin.add')}}">
                                        <i class="las la-minus"></i><span>Add</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ (request()->is('assistant-manager/payment/analysis/columbite/view')) ? 'active' : '' }}">
                            <a href="{{route('payment.analysis.columbite.view')}}">
                                <i class="las la-minus"></i><span>Columbite</span>
                            </a>
                            <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('assistant-manager/payment/analysis/columbite/add')) ? 'active' : '' }}">
                                    <a href="{{route('payment.analysis.columbite.add')}}">
                                        <i class="las la-minus"></i><span>Add</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
                @if(Auth::user()->account_type == 'Accountant')
                <li class="{{ (request()->is('accountant/expenses/*')) ? 'active' : '' }}">
                    <a href="#analysis" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-wallet-2-line mr-0"></i>
                        <span class="ml-4">Expenses</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('accountant/expenses/view')) ? 'active' : '' }}">
                            <a href="{{route('expenses.view')}}">
                                <i class="las la-minus"></i><span>View</span>
                            </a>
                            <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('accountant/expenses/add')) ? 'active' : '' }}">
                                    <a href="{{route('expenses.add')}}">
                                        <i class="las la-minus"></i><span>Add</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="{{ (request()->is('dashboard/notifications')) ? 'active' : '' }}">
                    <a href="{{route('notifications')}}" class="svg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="ml-4">Notifications</span>
                    </a>
                </li>
                <li class="{{ (request()->is('dashboard/profile*')) ? 'active' : '' }}">
                    <a href="#return" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-settings-line"></i>
                        <span class="ml-4">Settings</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="return" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('dashboard/profile')) ? 'active' : '' }}">
                            <a href="{{route('profile')}}">
                                <i class="las la-minus"></i><span>Profile</span>
                            </a>
                        </li>
                        <li class="">
                            <a data-toggle="modal" data-target="#logout" href="#">
                                <i class="las la-minus"></i><span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>