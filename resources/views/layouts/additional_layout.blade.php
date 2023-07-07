<div class="d-flex flex-wrap align-items-center justify-content-end mb-4">
    <a href="#" data-toggle="modal" data-target="#add" class="btn btn-primary add-list">Add Rate</a>
    <a href="#" class="add-list ml-3">
        <select class="selectpicker form-control" data-style="py-0" id="myselect" required>
            <option value="">-- Select Grade --</option>
            @if(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get()->count() > 0)
            @foreach(App\Models\BeratingCalculation::latest()->where('status', 'Active')->get() as $berating)
            <option value="{{$berating->id}}">{{$berating->grade}}</option>
            @endforeach
            @else
            <option value="">No Berating Added</option>
            @endif
        </select>
    </a>
</div>

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Berating</h5>
                <p class="text-danger">* Indicates required</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="popup text-left">
                    <div class="content create-workform bg-body">
                        <form action="{{route('add.berating.rate')}}" method="POST" data-toggle="validator">
                        @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Grade *</label>
                                        <input type="text" class="form-control" placeholder="Enter grade" name="grade" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price (per bag) *</label>
                                        <input type="number" class="form-control" placeholder="Enter price" name="price" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Unit Price (per pound) *</label>
                                        <input type="text" id="txtChar" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Enter unit price" name="unit_price" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Berating</h5>
                <p class="text-danger">* Indicates required</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="popup text-left">
                    <div class="content create-workform bg-body">
                        <form action="{{route('update.berating.rate')}}" method="POST">
                        @csrf
                            <input name="id" id="id" hidden>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Grade *</label>
                                        <input type="text" class="form-control" placeholder="Enter grade" name="grade" id="grade" readonly>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price (per bag) *</label>
                                        <input type="number" class="form-control" placeholder="Enter price" name="price" id="price" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Unit Price (per pound) *</label>
                                        <input type="number" class="form-control" placeholder="Enter unit price" name="unit_price" id="unit_price" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary mr-2">Update</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#myselect').change(function() {
        var opval = $(this).val();
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('get.berating.rate')}}",
            data: {
                'id': opval
            },
            success: function (response) {
                document.getElementById('id').value = response.id;
                document.getElementById('grade').value = response.grade;
                document.getElementById('price').value = response.price;
                document.getElementById('unit_price').value = response.unit_price;
            }
        });
        $('#update').modal("show");
    });
</script>