@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-12">
               <div class="card">
                  <div class="card-header d-flex justify-content-between">
                     <div class="header-title">
                        <h4 class="card-title">Notifications</h4>
                     </div>
                  </div>
                  <div class="card-body">
                     <p>All Notifications in one place</p>
                     @foreach($notifications as $notification)
                     @if($notification->status == 'Unread')
                     <div class="toast fade show bg-primary text-white border-0 rounded p-2" role="alert" aria-live="assertive" aria-atomic="true">
                        <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Click to read notification" href="{{route('admin.read.notification', Crypt::encrypt($notification->id))}}">
                            <div class="toast-header bg-primary text-white">
                            <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                <rect width="100%" height="100%" fill="#fff"></rect>
                            </svg>
                            <strong class="mr-auto text-white">{{$notification->title}}</strong>
                            <small class="text-white">{{$notification->created_at->diffForHumans()}}</small>
                            </div>
                            <div class="toast-body text-white">
                            {{$notification->body}}
                            </div>
                        </a>
                     </div>
                     @endif
                     @if($notification->status == 'Read')
                     <div class="toast fade show bg-success text-white border-0 rounded p-2 mt-3" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-success text-white">
                           <svg class="bd-placeholder-img rounded mr-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                              <rect width="100%" height="100%" fill="#fff"></rect>
                           </svg>
                           <strong class="mr-auto text-white">{{$notification->title}}</strong>
                           <small>{{$notification->created_at->diffForHumans()}}</small>
                           <!-- <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                           <span aria-hidden="true">×</span>
                           </button> -->
                        </div>
                        <div class="toast-body">
                        {{$notification->body}}
                        </div>
                     </div>
                     @endif
                  </div>
                  @endforeach
               </div>
            </div>
         </div>
      </div>
</div>
@endsection