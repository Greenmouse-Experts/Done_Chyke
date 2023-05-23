@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Notifications</h4>
                        <p class="mb-0">All notification in one place</p>
                    </div>

                    <!-- <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#"><i class="ri-home-4-line mr-1 float-left"></i>Home</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Library</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="col-sm-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Notifications</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    @if($notification->status == 'Unread')
                                    <div class="text-left toast fade show bg-primary text-white border-0 rounded p-2" role="alert" aria-live="assertive" aria-atomic="true">
                                        <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to read notification" href="{{route('read.notification', Crypt::encrypt($notification->id))}}">
                                            <div class="toast-header bg-primary text-white">
                                                <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                                    <rect width="100%" height="100%" fill="#fff"></rect>
                                                </svg>
                                                <strong class="mr-auto text-white">{{$notification->title}}</strong>
                                                <small class="text-white">{{$notification->created_at->diffForHumans()}}</small>
                                                <button type="button" class="mb-1 close text-white" style="font-size: 14px !important;" data-dismiss="toast" aria-label="Close">
                                                    <span aria-hidden="true" class="text-white">
                                                        @if($notification->admin_id == null)
                                                        @else
                                                        {{App\Models\User::find($notification->admin_id)->name}}
                                                        @endif
                                                    </span>
                                                </button>
                                            </div>
                                            <div class="toast-body text-white">
                                                {{$notification->body}}
                                            </div>
                                        </a>
                                    </div>
                                    @endif
                                    @if($notification->status == 'Read')
                                    <div class="text-left toast fade show bg-success text-white border-0 rounded p-2 mt-3" role="alert" aria-live="assertive" aria-atomic="true">
                                        <div class="toast-header bg-success text-white">
                                            <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                                <rect width="100%" height="100%" fill="#fff"></rect>
                                            </svg>
                                            <strong class="mr-auto text-white">{{$notification->title}}</strong>
                                            <small>{{$notification->created_at->diffForHumans()}}</small>
                                            <button type="button" class="mb-1 close text-white" style="font-size: 14px !important;" data-dismiss="toast" aria-label="Close">
                                                <span aria-hidden="true" class="text-white">
                                                    @if($notification->admin_id == null)
                                                    @else
                                                    {{App\Models\User::find($notification->admin_id)->name}}
                                                    @endif
                                                </span>
                                            </button>
                                        </div>
                                        <div class="toast-body">
                                            {{$notification->body}}
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection