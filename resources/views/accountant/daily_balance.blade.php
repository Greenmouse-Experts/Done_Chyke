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
                        </div>
                        <div class="card-body">
                            <form action="{{route('daily.balance.add')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Starting Balance for Today - {{\Carbon\Carbon::now()->toFormattedDateString()}}  *</label>
                                            <input type="number" class="form-control" placeholder="Enter startng balance" name="starting_balance" value="{{$starting_balance}}" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Additional Income for Today </label>
                                            <input type="number" class="form-control" placeholder="Enter additional income" value="{{$additional_income}}" name="additional_income">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Amount Used for Today </label>
                                            <input type="number" class="form-control" placeholder="Enter amount used" value="{{$amount_used}}" name="amount_used">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <button type="submit" name="save" value="save" class="btn btn-primary mr-2">Save</button>
                                    <button type="reset" class="btn btn-danger">Reset</button>
                                </div>
                            </form>
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
                                       </div> <span class="type text-white">Starting Balance</span>
                                    </div>
                                 </th>
                                 <th class="text-center prc-wrap">
                                    <div class="prc-box active">
                                       <div class="h3 pt-4 text-white">₦{{number_format($additional_income, 2)}}
                                       </div> <span class="type text-white">Additional Income</span>
                                    </div>
                                 </th>
                                 <th class="text-center prc-wrap">
                                    <div class="prc-box active">
                                       <div class="h3 pt-4 text-white">₦{{number_format($amount_used, 2)}}
                                       </div> <span class="type text-white">Amount <br> Used</span>
                                    </div>
                                 </th>
                                 <th class="text-center prc-wrap">
                                    <div class="prc-box active">
                                       <div class="h3 pt-4 text-white">₦{{number_format($remaining_balance, 2)}}
                                       </div> <span class="type text-white">Remaining Balance</span>
                                    </div>
                                 </th>
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection