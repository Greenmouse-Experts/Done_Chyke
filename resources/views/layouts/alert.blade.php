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