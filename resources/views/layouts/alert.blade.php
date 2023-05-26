@if($errors->any())
@foreach($errors->all() as $error)
<div class="col-12 mb-2">
    <div class="alert alert-danger alert-timeout alert-border-left text-center" style="padding: .7rem !important; justify-content: center !important;" role="alert">
        {{$error}}!
    </div>
</div>
@endforeach
@endif

@if(session()->has('type'))
<div class="col-12 mb-2">
    <div class="alert alert-{{session()->get('type')}} alert-timeout alert-border-left text-center" style="padding: .7rem !important; justify-content: center !important;" role="alert">
        {{session()->get('message')}}
    </div>
</div>
@endif

@if(session()->has('alertType'))
<div class="modal fade show" id="sweetModal" tabindex="-2" role="dialog" style="background-color: rgba(0,0,0,0.4); padding-right: 4px; display: block; align-items: center !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-center">
                    <div class="content create-workform bg-body mt-4">
                        <div class="success_icon succes-animation icon-top"><i class="ri-check-line"></i></div>
                        <p class="text-success" style="margin-top: 4rem;">Success!</p>
                        <p style="font-size: 24px;">{{session()->get('message')}}</p>
                        <button type="button" class="btn btn-secondary mr-3" id="close">Close</button>
                        @if(session()->has('back'))
                        <a type="button" class="btn btn-secondary" href="{{session()->get('back')}}">Back</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if(session()->has('previewPrice'))
<div class="modal fade show" id="sweetModal" tabindex="-2" role="dialog" style="background-color: rgba(0,0,0,0.4); padding-right: 4px; display: block; align-items: center !important;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="popup text-center">
                    <div class="content create-workform bg-body mt-4">
                        <p style="font-size: 1.3rem;"><b>Total Price: ₦{{number_format(session()->get('message'), 2)}}</b></p>
                        <button type="button" class="btn btn-secondary" id="close">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    // Get the modal
    var modal = document.getElementById("sweetModal");

    // Get the <span> element that closes the modal
    var span = document.getElementById("close");

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
    modal.style.display = "none";
    }
</script>

<style>
    .success_icon {
        background-color: #4BB543;
        position: absolute;
        border-radius: 50%;
        top: 0px;
        left: 50%;
        margin-top: -40px;
        margin-left: -40px;
        width: 80px;
        height: 80px;
        font-size: 30px;
        color: #fff;
        line-height: 80px;
        text-align: center;
    }
    .succes-animation {
        animation: succes-pulse 2s infinite;
    }
    @keyframes succes-pulse { 
    0% {
        box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .2);
    }
    50% {
        box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .4);
    }
    100% {
        box-shadow: 0px 0px 30px 20px rgba(75, 181, 67, .2);
    }
    }
</style>