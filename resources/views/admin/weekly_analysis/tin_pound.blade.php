@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#"></a></li> -->
                    <li class="breadcrumb-item active" aria-current="page">Tin(Pound) Summary</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Summary of Tin (Pound) Material Collected</h4>
                        <p class="mb-0">Overall summary of material collected in one place</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div style="justify-content: flex-start;">
                            <form action="{{ route('admin.weekly.analysis.tin.pound')}}" method="POST" data-toggle="validator">
                                @csrf
                                <label class="mr-2"><strong>Start Date :</strong>
                                <input type="date" name="start_date" class="form-control" required>
                                </label>&nbsp;&nbsp;
                                <label class="mr-2"><strong>End Date :</strong>
                                <input type="date" name="end_date" class="form-control" required>
                                </label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                        <div style="justify-content: flex-end;">
                            <form action="{{ route('admin.weekly.analysis.tin.pound')}}" method="POST" data-toggle="validator">
                                @csrf
                                <select class="form-control">
                                    <option>-- Select Manager --</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info" id="tin-pound">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>Date</th>
                                <th>18.8</th>
                                <th>18.9</th>
                                <th>19.0</th>
                                <th>19.1</th>
                                <th>19.2</th>
                                <th>19.3</th>
                                <th>19.4</th>
                                <th>19.5</th>
                                <th>19.6</th>
                                <th>19.7</th>
                                <th>19.8</th>
                                <th>19.9</th>
                                <th>20.0</th>
                                <th>20.1</th>
                                <th>20.2</th>
                                <th>20.3</th>
                                <th>20.4</th>
                                <th>20.5</th>
                            </tr>
                            <tbody class="ligth-body">
                            @foreach($analysis as $anana)
                            <tr>
                                <td>{{$anana['date']}}</td>
                                <td>
                                    @if ($anana['berating'] == '18.8') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                @if ($anana['berating'] == '18.9') 
                                    {{$anana['total']}}
                                     @endif
                                </td>
                                <td> 
                                    @if ($anana['berating'] == '19.0') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td> 
                                    @if ($anana['berating'] == '19.1') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.2') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.3') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.4') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.5') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.6') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.7') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.8') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '19.9') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.0') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.1') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.2') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.3') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.4') 
                                    {{$anana['total']}}
                                    @endif
                                </td>
                                <td>
                                    @if ($anana['berating'] == '20.5') 
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
                        <h4 class="mb-3">Weekly Production</h4>
                        <div class="row w-100 mt-5">
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">18 Material</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['18M']['bags']}}<sup>{{$data['18M']['pounds']}}</sup></p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">19 Material</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['19M']['bags']}}<sup>{{$data['19M']['pounds']}}</sup></p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">20 Material</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['20M']['bags']}}<sup>{{$data['20M']['pounds']}}</sup></p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">Total (bags)</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['TOTAL_BAGS']['bags']}}<sup>{{$data['TOTAL_BAGS']['pounds']}}</sup></p>
                            </div>
                            <div class="col">
                                <p style="font-weight:600; font-size:18px">Average Berating</p>
                                <p style="margin-left: 25px; font-size: 32px; margin-top: 15px;">{{$data['AB']}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() { 
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });       


    $('#filterTable').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.weekly.analysis.tin.pounds') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: (data) => {
                console.log(data);
                $('#tin-pound').dataTable({
                    destroy: true,
                    processing: true,
                    serverSide: true,
                    columns: [
                        {
                            data: 'data.date',
                            name: 'date'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '18.8') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '18.8'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '18.9') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '18.9'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.0') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.0'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.1') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.1'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.2') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.2'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.3') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.3'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.4') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.4'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.5') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.5'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.6') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.6'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.7') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.7'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.8') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.8'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '19.9') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '19.9'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.0') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.0'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.1') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.1'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.2') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.2'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.3') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.3'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.4') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.4'
                        },
                        {
                            "render": function(data, type, row) {

                                if (row.berating === '20.5') {
                                    return row.total;
                                } else {
                                    return "";
                                }
                            },
                            name: '20.5'
                        },
                    ],
                    order: [
                        [0, 'desc']
                    ] 
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
    });
</script>
@endsection