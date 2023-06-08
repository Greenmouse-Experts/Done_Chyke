<div class="iq-sidebar sidebar-default ">
    <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
        <a href="{{route('admin.dashboard')}}" class="header-logo">
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
                <li class="{{ (request()->is('admin/dashboard')) ? 'active' : '' }}">
                    <a href="{{route('admin.dashboard')}}" class="svg-icon">
                        <svg class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Dashboard</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/staff*')) ? 'active' : '' }}">
                    <a href="{{route('admin.staff')}}">
                        <svg class="svg-icon" id="p-dash8" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="ml-4">Staff</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/rates/list*')) ? 'active' : '' }}">
                    <a href="#rates-list" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-calculator-line mr-0"></i>
                        <span class="ml-4">Rates List</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="rates-list" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/rates/list/berating')) ? 'active' : '' }}">
                            <a href="{{route('admin.rates.berating')}}">
                                <i class="las la-minus"></i><span>Berating</span>
                            </a>
                        </li>
                        <li class="{{ (request()->is('admin/rates/list/analysis')) ? 'active' : '' }}">
                            <a href="{{route('admin.rates.analysis')}}">
                                <i class="las la-minus"></i><span>Analysis</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/payment/receipt*')) ? 'active' : '' }}">
                    <a href="#receipt" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-calculator-line mr-0"></i>
                        <span class="ml-4">Payment Receipt</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="receipt" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/payment/receipt/tin/view')) ? 'active' : '' }}">
                            <a href="{{route('admin.payment.receipt.tin.view')}}">
                                <i class="las la-minus"></i><span>Tin</span>
                            </a>
                        </li>
                        <li class="{{ (request()->is('admin/payment/receipt/columbite/view')) ? 'active' : '' }}">
                            <a href="{{route('admin.payment.receipt.columbite.view')}}">
                                <i class="las la-minus"></i><span>Columbite</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/weekly/analysis*')) ? 'active' : '' }}">
                    <a href="#analysis" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <svg class="svg-icon" id="p-dash7" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span class="ml-4">Weekly Analysis</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/weekly/analysis/tin/pound')) ? 'active' : '' }}">
                            <a href="{{route('admin.weekly.analysis.tin.pound')}}">
                                <i class="las la-minus"></i><span>Tin (Pound)</span>
                            </a>
                        </li>
                        <li class="{{ (request()->is('admin/weekly/analysis/tin/kg')) ? 'active' : '' }}">
                            <a href="{{route('admin.weekly.analysis.tin.kg')}}">
                                <i class="las la-minus"></i><span>Tin (Kg)</span>
                            </a>
                        </li>
                        <li class="{{ (request()->is('admin/weekly/analysis/columbite/pound')) ? 'active' : '' }}">
                            <a href="{{route('admin.weekly.analysis.columbite.pound')}}">
                                <i class="las la-minus"></i><span>Columbite (Pound)</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/expenses*')) ? 'active' : '' }}">
                    <a href="{{route('admin.expenses')}}" class="svg-icon">
                        <svg class="svg-icon" id="p-dash5" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        <span class="ml-4">Expenses</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/transactions')) ? 'active' : '' }}">
                    <a href="{{route('admin.transactions')}}" class="svg-icon">
                        <svg class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="ml-4">Transactions</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/notifications')) ? 'active' : '' }}">
                    <a href="{{route('admin.notifications')}}" class="svg-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="ml-4">Notifications</span>
                    </a>
                </li>
                <li class="{{ (request()->is('admin/profile*')) ? 'active' : '' }}">
                    <a href="#return" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-settings-line"></i>
                        <span class="ml-4">Settings</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="return" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        @if(Auth::user()->role == 'Master')
                        <li class="{{ (request()->is('admin/sub/admins')) ? 'active' : '' }}">
                            <a href="{{route('admin.sub.admins')}}">
                                <i class="las la-minus"></i><span>Sub Admins</span>
                            </a>
                        </li>
                        @endif
                        <li class="{{ (request()->is('admin/profile')) ? 'active' : '' }}">
                            <a href="{{route('admin.profile')}}">
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