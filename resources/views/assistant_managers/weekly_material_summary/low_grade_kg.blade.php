@extends('layouts.dashboard_frontend')

@section('page-content')

<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Weekly Material Summary for Low Grade (Kg)</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Low Grade (Kg) Summary</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div style="justify-content: flex-start;">
                            <form action="{{ route('weekly.material.summary.low.grade.pound')}}" method="POST" data-toggle="validator">
                                @csrf
                                <label class="mr-2"><strong>Start Date :</strong>
                                <input type="date" name="start_date" class="form-control" value="{{$start_date}}">
                                </label>&nbsp;&nbsp;
                                <label class="mr-2"><strong>End Date :</strong>
                                <input type="date" name="end_date" class="form-control" value="{{$end_date}}">
                                </label>
                                <label>
                                    <select class="form-control" name="manager">
                                        @if($manager == null)
                                        <option value="">-- Select Manager --</option>
                                        @else
                                        <option value="{{$manager}}">{{App\Models\User::find($manager)->name}}</option>
                                        @endif
                                        @if(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get()->count() > 0)
                                        @foreach(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get() as $manager)
                                        <option value="{{$manager->id}}">{{$manager->name}}</option>
                                        @endforeach
                                        @else
                                        <option value="">No Manager Added</option>
                                        @endif
                                    </select>
                                </label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info" id="tin-pound">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Date</th>
                                <th>21.0</th>
                                <th>21.1</th>
                                <th>21.2</th>
                                <th>21.3</th>
                                <th>21.4</th>
                                <th>21.5</th>
                                <th>21.6</th>
                                <th>21.7</th>
                                <th>21.8</th>
                                <th>21.9</th>
                                <th>22.0</th>
                                <th>22.1</th>
                                <th>22.2</th>
                                <th>22.3</th>
                                <th>22.4</th>
                                <th>22.5</th>
                                <th>22.6</th>
                                <th>22.7</th>
                                <th>22.8</th>
                                <th>22.9</th>
                                <th>23.0</th>
                                <th>23.1</th>
                                <th>23.2</th>
                                <th>23.3</th>
                                <th>23.4</th>
                                <th>23.5</th>
                            </tr>
                            <tbody class="ligth-body">
                                @foreach($analysis as $anana)
                                <tr>
                                    <td>{{$anana['date']}}</td>
                                    <td>
                                        @if ($anana['berating'] == '21.0') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.1') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.2') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.3') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.4') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td> 
                                        @if ($anana['berating'] == '21.5') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td> 
                                        @if ($anana['berating'] == '21.6') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.7') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.8') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '21.9') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.0') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.1') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.2') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.3') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.4') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.5') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.6') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.7') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.8') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '22.9') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.0') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.1') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.2') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.3') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.4') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($anana['berating'] == '23.5') 
                                            {{$anana['total']}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
        <div class="row">
            <div class="col-lg-12">
                <div class="mt-5 d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div class="w-100">
                        <h4 class="mb-3">Production</h4>
                        <div class="row w-100 mt-5">
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">Total (bags)</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['TOTAL_BAGS']['bags']}}<sup>{{$data['TOTAL_BAGS']['pounds']}}</sup></p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">Average Berating</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['AB']}}</p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">Percentage (%)</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['AP']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection