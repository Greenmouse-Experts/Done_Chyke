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
                    <a href="#people" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <svg class="svg-icon" id="p-dash8" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span class="ml-4">Staffs</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/staff/managers')) ? 'active' : '' }}">
                            <a href="{{route('admin.managers')}}">
                                <i class="las la-minus"></i><span>Managers</span>
                            </a>
                            <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('admin/staff/manager/add')) ? 'active' : '' }}">
                                    <a href="{{route('admin.add.manager')}}">
                                        <i class="las la-minus"></i><span>Add Manager</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ (request()->is('admin/staff/accountants')) ? 'active' : '' }}">
                            <a href="{{route('admin.accountants')}}">
                                <i class="las la-minus"></i><span>Accountants</span>
                            </a>
                            <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('admin/staff/accountant/add')) ? 'active' : '' }}">
                                    <a href="{{route('admin.add.accountant')}}">
                                        <i class="las la-minus"></i><span>Add Accountant</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ (request()->is('admin/staff/assistance/manager')) ? 'active' : '' }}">
                            <a href="{{route('admin.manager.assistances')}}">
                                <i class="las la-minus"></i><span>Assistant Manager</span>
                            </a>
                            <ul id="people" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('admin/staff/assistance/manager/add')) ? 'active' : '' }}">
                                    <a href="{{route('admin.add.manager.assistance')}}">
                                        <i class="las la-minus"></i><span>Add Assistant Manager</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/rates*')) ? 'active' : '' }}">
                    <a href="#calculation" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-calculator-line mr-0"></i>
                        <span class="ml-4">Rates</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="calculation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/rates/berating')) ? 'active' : '' }}">
                            <a href="{{route('admin.rates.berating')}}">
                                <i class="las la-minus"></i><span>Berating</span>
                            </a>
                            <ul id="calculation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('admin/rates/berating/add')) ? 'active' : '' }}">
                                    <a href="{{route('admin.add.rate.berating')}}">
                                        <i class="las la-minus"></i><span>Add Berating</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="{{ (request()->is('admin/rates/analysis')) ? 'active' : '' }}">
                            <a href="{{route('admin.rates.analysis')}}">
                                <i class="las la-minus"></i><span>Analysis</span>
                            </a>
                            <ul id="calculation" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                                <li class="{{ (request()->is('admin/rates/analysis/add')) ? 'active' : '' }}">
                                    <a href="{{route('admin.add.rate.analysis')}}">
                                        <i class="las la-minus"></i><span>Add Analysis</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/payment/voucher*')) ? 'active' : '' }}">
                    <a href="#analysis" class="collapsed" data-toggle="collapse" aria-expanded="false">
                        <i class="ri-calculator-line mr-0"></i>
                        <span class="ml-4">Payment Voucher</span>
                        <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="10 15 15 20 20 15"></polyline>
                            <path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                        </svg>
                    </a>
                    <ul id="analysis" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                        <li class="{{ (request()->is('admin/payment/voucher/tin/view')) ? 'active' : '' }}">
                            <a href="{{route('admin.payment.voucher.tin.view')}}">
                                <i class="las la-minus"></i><span>Tin</span>
                            </a>
                        </li>
                        <li class="{{ (request()->is('admin/payment/voucher/columbite/view')) ? 'active' : '' }}">
                            <a href="{{route('admin.payment.voucher.columbite.view')}}">
                                <i class="las la-minus"></i><span>Columbite</span>
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