@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Daily Balance</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daily Balance</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">{{\Carbon\Carbon::now()->toFormattedDateString()}}</h4>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
                                <button data-toggle="modal" data-target="#add_starting_balance" class="btn btn-primary text-white add-list"><i class="las la-plus mr-3"></i>Add Starting Balance</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
               <div class="card">
                  <div class="card-body">
                     <div class="table-responsive pricing pt-2">
                        <table id="my-table" class="table">
                           <thead>
                              <tr>
                                 <th class="text-center prc-wrap">
                                    <div class="prc-box active">
                                       <div class="h3 pt-4 text-white">₦{{number_format($starting_balance, 2)}}
                                       </div> <span class="type text-white">Starting <br> Balance</span>
                                    </div>
                                 </th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>S/N</th>
                                <th>Date</th>
                                <th>Starting Balance</th>
                                <th>Miscellaneous Expenses</th>
                                <th>Cash</th>
                                <th>Transfer</th>
                                <th>Transfer by Cheques</th>
                                <th>Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($balances as $balance)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>
                                <td>{{$balance->date}}</td>
                                <td>₦{{number_format($balance->starting_balance, 2)}}</td>
                                <td>₦{{number_format($balance->expense, 2)}}</td>
                                <td>₦{{number_format($balance->cash, 2)}}</td>
                                <td>₦{{number_format($balance->transfer, 2)}}</td>
                                <td>₦{{number_format($balance->transfer_by_cheques, 2)}}</td>
                                <td>₦{{number_format($balance->closing_balance, 2)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="font-size: 1.1rem; font-weight: 700">Total</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('starting_balance'), 2)}}</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('expense'), 2)}}</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('cash'), 2)}}</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('transfer'), 2)}}</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('transfer_by_cheques'), 2)}}</td>
                                <td colspan="1" style="font-size: 1.1rem; font-weight: 700">₦{{number_format($balances->sum('closing_balance'), 2)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<div class="modal fade" id="add_starting_balance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add</h5>&nbsp;
                 <p class="text-danger"> * Indicates required</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="popup text-left">
                    <form action="{{route('daily.balance.add')}}" method="POST" data-toggle="validator">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Starting Balance for Today - {{\Carbon\Carbon::now()->toFormattedDateString()}}  *</label>
                                    <input type="number" class="form-control" placeholder="Enter startng balance" name="starting_balance" value="{{$starting_balance}}" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection