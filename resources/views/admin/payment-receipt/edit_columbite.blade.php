@extends('layouts.admin_frontend')

@section('page-content')
<div class="content-page">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-3">Edit Columbite Payment Receipts</h4>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="ri-home-4-line mr-1 float-left"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.payment.receipt.columbite.view', 'pound')}}">Columbite Payment Receipts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                @if($columbitePayment->type == 'pound') 
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <p class="text-danger"> * Indicates required</p>
                        </div>
                        @include('layouts.additional_layout')
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-pound-tab-fill" data-toggle="pill" href="#pills-pound-fill" role="tab" aria-controls="pills-pound" aria-selected="true">COLUMBITE (POUND)</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-1">
                            <div class="tab-pane fade active show" id="pills-pound-fill" role="tabpanel" aria-labelledby="pills-home-tab-fill">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="pound-button" action="{{route('admin.payment.receipt.columbite.pound.update', Crypt::encrypt($columbitePayment->id))}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                            @csrf
                                            <input name="type" value="pound" hidden/>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Supplier Name *</label>
                                                        <input type="text" class="form-control" placeholder="Enter supplier name" name="supplier" value="{{$columbitePayment->supplier}}" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rates List (Berating) *</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="grade" required>
                                                            <option value="{{$columbitePayment->grade}}">{{App\Models\BeratingCalculation::find($columbitePayment->grade)->grade}}</option>
                                                            <option value="">-- Select Berating --</option>
                                                            @if(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get()->count() > 0)
                                                                @foreach(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get() as $grade)
                                                                <option value="{{$grade->id}}">{{$grade->grade}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Berating Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Percentage (%) Analysis</label>
                                                        <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter percentage analysis value" value="{{$columbitePayment->percentage_analysis}}" name="percentage">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Weight</label>
                                                        <br>
                                                        <input type="radio" name="poundweight" value="bag" @if($columbitePayment->bag !== 0) checked @endif/> Bag
                                                        <input type="radio" name="poundweight" style="margin-left: 2rem;" value="pound"  @if($columbitePayment->bag == 0) checked @endif/> Pound
                                                    </div>
                                                </div>
                                                <div id="poundweightbag" class="desc col-12" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bag</label>
                                                                <input type="number" class="form-control" placeholder="Enter bags value" value="{{$columbitePayment->bag}}" name="bags">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Pound</label>
                                                                <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter pound value" onkeyup="this.value = minmax(this.value, null, 79)" value="{{$columbitePayment->pound}}" name="bag_pound">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="poundweightpound" class="desc col-12" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Pound</label>
                                                        <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter pound value" value="{{$columbitePayment->pound}}" name="pounds">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Manager *</label>
                                                        <select name="manager" class="selectpicker form-control" data-style="py-0" required>
                                                            <option value="{{$columbitePayment->staff}}">{{App\Models\User::find($columbitePayment->staff)->name}}</option>
                                                            <option value="">-- Select Manager --</option>
                                                            @if(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get()->count() > 0)
                                                                @foreach(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get() as $manager)
                                                                <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Manager Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Date of Purchase *</label>
                                                        <input type="date" class="form-control" placeholder="Enter date" name="date_of_purchase" value="{{$columbitePayment->date_of_purchase}}" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Receipt Number *</label>
                                                        <input type="text" class="form-control" placeholder="Enter receipt number" name="receipt_no" value="{{$columbitePayment->receipt_no}}"required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Receipt Image</label>
                                                        <input type="file" class="form-control" placeholder="Upload receipt" name="receipt_image" value="{$columbitePayment->receipt_image}}" accept="image/png, image/jpeg, image/jpg">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5">
                                                <a href="#" onclick="event.preventDefault();
                                                        document.getElementById('pound-button').submit();"class="btn btn-primary mr-2" name="save" value="preview" >Preview Price</a>
                                                <button type="submit" name="save" value="save" class="btn btn-primary mr-2">Update</button>
                                                <button type="reset" class="btn btn-danger">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($columbitePayment->type == 'kg')
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <p class="text-danger"> * Indicates required</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-pound-tab-fill" data-toggle="pill" href="#pills-kg-fill" role="tab" aria-controls="pills-pound" aria-selected="true">COLUMBITE (KG)</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-1">
                            <div class="tab-pane fade active show" id="pills-kg-fill" role="tabpanel" aria-labelledby="pills-home-tab-fill">
                                <div class="card" style="border: 1px solid #c7cbd3 !important;">
                                    <div class="card-body">
                                        <form id="kg-button" action="{{route('admin.payment.receipt.columbite.kg.update', Crypt::encrypt($columbitePayment->id))}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                            @csrf
                                            <input name="type" value="kg" hidden />
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Supplier Name *</label>
                                                        <input type="text" class="form-control" placeholder="Enter supplier name" name="supplier" value="{{$columbitePayment->supplier}}" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Rates List (Berating) *</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="grade" required>
                                                            <option value="{{$columbitePayment->grade}}">{{App\Models\BeratingCalculation::find($columbitePayment->grade)->grade}}</option>
                                                            <option value="">-- Select Grade --</option>
                                                            @if(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get()->count() > 0)
                                                            @foreach(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get() as $berating)
                                                            <option value="{{$berating->id}}">{{$berating->grade}}</option>
                                                            @endforeach
                                                            @else
                                                            <option value="">No Berating Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Percentage (%) Analysis *</label>
                                                        <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter percentage analysis value" value="{{$columbitePayment->percentage_analysis}}" name="percentage">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Weight *</label>
                                                        <br>
                                                        <input type="radio" name="kgweight" value="bag" @if($columbitePayment->bag !== 0) checked @endif/> Bag
                                                        <input type="radio" name="kgweight" style="margin-left: 2rem;" value="kg" @if($columbitePayment->bag == 0) checked @endif/> Kg
                                                    </div>
                                                </div>
                                                <div id="kgweightbag" class="desc col-12" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bag</label>
                                                                <input type="number" class="form-control" placeholder="Enter bags value" value="{{$columbitePayment->bag}}" name="bags">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Kg</label>
                                                                <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter kg value" onkeyup="this.value = minmax(this.value, null, 49)" value="{{$columbitePayment->kg}}" name="bag_kg">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="kgweightkg" class="desc col-12" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Kg</label>
                                                        <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter kg value" value="{{$columbitePayment->kg}}" name="kg">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Manager *</label>
                                                        <select name="manager" class="selectpicker form-control" data-style="py-0" required>
                                                            <option value="{{$columbitePayment->staff}}">{{App\Models\User::find($columbitePayment->staff)->name}}</option>
                                                            <option value="">-- Select Manager --</option>
                                                            @if(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get()->count() > 0)
                                                                @foreach(App\Models\User::latest()->where('account_type', 'Manager')->where('status', '1')->get() as $manager)
                                                                <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Manager Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Date of Purchase *</label>
                                                        <input type="date" class="form-control" placeholder="Enter date" name="date_of_purchase" value="{{$columbitePayment->date_of_purchase}}" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Receipt Number *</label>
                                                        <input type="text" class="form-control" placeholder="Enter receipt number" name="receipt_no" value="{{$columbitePayment->receipt_no}}"required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Receipt Image</label>
                                                        <input type="file" class="form-control" placeholder="Upload receipt" name="receipt_image" value="{$columbitePayment->receipt_image}}" accept="image/png, image/jpeg, image/jpg">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5">
                                                <a href="#" onclick="event.preventDefault();
                                                        document.getElementById('kg-button').submit();" class="btn btn-primary mr-2" name="save" value="preview">Preview Price</a>
                                                <button type="submit" name="save" value="save" class="btn btn-primary mr-2">Update</button>
                                                <button type="reset" class="btn btn-danger">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<script>
    function minmax(value, min, max) 
    {
        if(parseInt(value) < min || isNaN(parseInt(value))) 
            return min; 
        else if(parseInt(value) > max) 
            return max; 
        else return value;
    }

    $(document).ready(function() {
        $("input[name$='poundweight']").click(function() {
            var test = $(this).val();

            $("div.desc").hide();
            $("#poundweight" + test).show();
        });
    });

    $(document).ready(function() {
        $("input[name$='kgweight']").click(function() {
            var test = $(this).val();

            $("div.desc").hide();
            $("#kgweight" + test).show();
        });
    });

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 
        && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }
    
</script>
@endsection