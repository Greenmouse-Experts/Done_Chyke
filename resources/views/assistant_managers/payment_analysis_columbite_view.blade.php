@extends('layouts.dashboard_frontend')

@section('page-content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Columbite Payment Analysis</h4>
                        <p class="mb-0">All columbite payment analysis in one place </p>
                    </div>
                    <a href="{{route('payment.analysis.columbite.add')}}" class="btn btn-primary add-list"><i class="las la-plus mr-3"></i>Add</a>
                </div>
            </div>
            

            
        </div>
        <!-- Page end  -->
    </div>
</div>
@endsection