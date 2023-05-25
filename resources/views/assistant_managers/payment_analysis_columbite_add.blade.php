@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add Columbite Payment Analysis</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3 nav-fill" id="pills-tab-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-pound-tab-fill" data-toggle="pill" href="#pills-pound-fill" role="tab" aria-controls="pills-pound" aria-selected="true">COLUMBITE (POUND)</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-kg-tab-fill" data-toggle="pill" href="#pills-kg-fill" role="tab" aria-controls="pills-kg" aria-selected="false">COLUMBITE (KG)</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent-1">
                            <div class="tab-pane fade active show" id="pills-pound-fill" role="tabpanel" aria-labelledby="pills-home-tab-fill">
                                <div class="card" style="border: 1px solid #c7cbd3 !important;">
                                    <div class="card-body">
                                        <form id="preview-button" action="#" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                            @csrf
                                            <input name="type" value="pound" hidden/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Customer *</label>
                                                        <input type="text" class="form-control" placeholder="Enter customer name" name="customer" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Berating</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="berating" required>
                                                            <option value="">-- Select Berating --</option>
                                                            @if(App\Models\BeratingCalculation::latest()->get()->count() > 0)
                                                                @foreach(App\Models\BeratingCalculation::latest()->get() as $berating)
                                                                <option value="{{$berating->id}}">{{$berating->grade}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Berating Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Percentage (%) Analysis</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="berating" required>
                                                            <option value="">-- Select Percentage (%) Analysis --</option>
                                                            @if(App\Models\AnalysisCalculation::latest()->get()->count() > 0)
                                                                @foreach(App\Models\AnalysisCalculation::latest()->get() as $analysis)
                                                                <option value="{{$berating->id}}">{{$analysis->percentage}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Percentage Analysis Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Weight</label>
                                                        <br>
                                                        <input type="radio" name="weight" value="bag" /> Bag
                                                        <input type="radio" name="weight" style="margin-left: 2rem;" value="pound"/> Pound
                                                    </div>
                                                </div>
                                                <div id="weightbag" class="desc col-12" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bags</label>
                                                                <input type="number" class="form-control" placeholder="Enter bags value" name="bags">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Pounds</label>
                                                                <input type="number" class="form-control" placeholder="Enter pounds value" value="0" name="bag_pounds">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="weightpound" class="desc col-12" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Pounds</label>
                                                        <input type="number" class="form-control" placeholder="Enter pounds value" name="pounds">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Managers</label>
                                                        <select name="manager" class="selectpicker form-control" data-style="py-0" required>
                                                            <option value="">-- Select Manager --</option>
                                                            @if(App\Models\Manager::latest()->get()->count() > 0)
                                                                @foreach(App\Models\Manager::latest()->get() as $manager)
                                                                <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Manager Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Date</label>
                                                        <input type="date" class="form-control" placeholder="Enter date" name="date" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Receipt</label>
                                                        <input type="file" class="form-control" placeholder="Upload receipt" name="receipt" accept="image/png, image/jpeg, image/jpg" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5">
                                                <a href="#" class="btn btn-primary mr-2" name="save" value="preview" >Preview Price</a>
                                                <button type="submit" name="save" value="save" class="btn btn-primary mr-2">Save</button>
                                                <button type="reset" class="btn btn-danger">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pills-kg-fill" role="tabpanel" aria-labelledby="pills-kg-tab-fill">
                                <div class="card" style="border: 1px solid #c7cbd3 !important;">
                                    <div class="card-body">
                                        <form id="preview-button" action="#" method="POST" data-toggle="validator" enctype="multipart/form-data">
                                            @csrf
                                            <input name="type" value="kg" hidden/>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Customer *</label>
                                                        <input type="text" class="form-control" placeholder="Enter customer name" name="customer" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Berating</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="berating" required>
                                                            <option value="">-- Select Berating --</option>
                                                            @if(App\Models\BeratingCalculation::latest()->get()->count() > 0)
                                                                @foreach(App\Models\BeratingCalculation::latest()->get() as $berating)
                                                                <option value="{{$berating->id}}">{{$berating->grade}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Berating Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Percentage (%) Analysis</label>
                                                        <select class="selectpicker form-control" data-style="py-0" name="berating" required>
                                                            <option value="">-- Select Percentage (%) Analysis --</option>
                                                            @if(App\Models\AnalysisCalculation::latest()->get()->count() > 0)
                                                                @foreach(App\Models\AnalysisCalculation::latest()->get() as $analysis)
                                                                <option value="{{$berating->id}}">{{$analysis->percentage}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Percentage Analysis Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Weight</label>
                                                        <br>
                                                        <input type="radio" name="weight" value="bag" /> Bag
                                                        <input type="radio" name="weight" style="margin-left: 2rem;" value="pound"/> Pound
                                                    </div>
                                                </div>
                                                <div id="weightbag" class="desc col-12" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Bags</label>
                                                                <input type="number" class="form-control" placeholder="Enter bags value" name="bags">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Pounds</label>
                                                                <input type="number" class="form-control" placeholder="Enter pounds value" value="0" name="bag_pounds">
                                                                <div class="help-block with-errors"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="weightpound" class="desc col-12" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Pounds</label>
                                                        <input type="number" class="form-control" placeholder="Enter pounds value" name="pounds">
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Managers</label>
                                                        <select name="manager" class="selectpicker form-control" data-style="py-0" required>
                                                            <option value="">-- Select Manager --</option>
                                                            @if(App\Models\Manager::latest()->get()->count() > 0)
                                                                @foreach(App\Models\Manager::latest()->get() as $manager)
                                                                <option value="{{$manager->id}}">{{$manager->name}}</option>
                                                                @endforeach
                                                            @else
                                                            <option value="">No Manager Added</option>
                                                            @endif
                                                        </select>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Date</label>
                                                        <input type="date" class="form-control" placeholder="Enter date" name="date" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Receipt</label>
                                                        <input type="file" class="form-control" placeholder="Upload receipt" name="receipt" accept="image/png, image/jpeg, image/jpg" required>
                                                        <div class="help-block with-errors"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5">
                                                <a href="#" class="btn btn-primary mr-2" name="save" value="preview" >Preview Price</a>
                                                <button type="submit" name="save" value="save" class="btn btn-primary mr-2">Save</button>
                                                <button type="reset" class="btn btn-danger">Reset</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
</div>

<script>
    $(document).ready(function() {
        $("input[name$='weight']").click(function() {
            var test = $(this).val();

            $("div.desc").hide();
            $("#weight" + test).show();
        });
    });
</script>
@endsection