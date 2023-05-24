@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Assistants Manager List</h4>
                        <p class="mb-0">All your Assistants Manager List in one place</p>
                    </div>
                    <a href="{{route('admin.add.manager.assistance')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add Assistant Manager</a>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Photo</th>
                                <th>Account Type</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Gender</th>
                                <th>Status</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach(App\Models\User::latest()->where('account_type', 'Assistant Manager')->get() as $assistance)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>
                                @if($assistance->avatar)
                                    <img class="rounded" width="50" src="{{$assistance->avatar}}" alt="{{$assistance->first_name}}">
                                @else
                                <div class="avatar-xs" style="display: inline-block; vertical-align: middle;">
                                    <span class="img-fluid rounded" style="background: #c56963; padding: 0.5rem; color: #fff;">
                                        {{ ucfirst(substr($assistance->name, 0, 1)) }} {{ ucfirst(substr($assistance->name, 1, 1)) }}
                                    </span>
                                </div>
                                @endif
                                </td>
                                <td>{{$assistance->account_type}}</td>
                                <td>{{$assistance->name}}</td>
                                <td>{{$assistance->email}}</td>
                                <td>{{$assistance->phone}}</td>
                                <td>{{$assistance->gender}}</td>
                                <td>
                                    @if($assistance->status == '1')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{$assistance->created_at->toDayDateTimeString()}}</td>
                                <td>
                                    <div class="d-flex align-items-center list-action">
                                        <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="View/Edit" href="{{route('admin.edit.manager.assistance', Crypt::encrypt($assistance->id))}}"><i class="ri-eye-line mr-0"></i></a>
                                        @if($assistance->status == '1')
                                        <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Deactivate"href="{{route('admin.deactivate.staff', Crypt::encrypt($assistance->id))}}"><i class="ri-stop-circle-line mr-0"></i></a>
                                        @else
                                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Active" href="{{route('admin.activate.staff', Crypt::encrypt($assistance->id))}}"><i class="ri-play-line mr-0"></i></a>
                                        @endif
                                        <a class="badge bg-danger mr-2" data-toggle="modal" data-target="#delete-{{$assistance->id}}" href="#"><i class="ri-delete-bin-line mr-0"></i></a>
                                        <div class="modal fade" id="delete-{{$assistance->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <div class="popup text-left">
                                                            <h4 class="mb-3">Are you sure, you want to delete this user?</h4>
                                                            <div class="content create-workform bg-body">
                                                                <form action="{{route('admin.delete.staff', Crypt::encrypt($assistance->id))}}" method="post">
                                                                    @csrf
                                                                    <div class="col-lg-12 mt-4">
                                                                        <div class="d-flex flex-wrap align-items-ceter justify-content-center">
                                                                            <div class="btn btn-primary mr-4" data-dismiss="modal">Cancel</div>
                                                                            <button type="submit" class="btn btn-primary mr-2">Delete</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection