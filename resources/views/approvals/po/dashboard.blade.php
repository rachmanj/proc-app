@extends('layout.main')

@section('title_page')
    Approvals
@endsection

@section('breadcrumb_title')
    <small>
        approvals / purchase order / dashboard
    </small>
@endsection





@section('content')
    <div class="row">
        <div class="col-12">

            <x-aprv-po-links page="dashboard" />



            {{-- @include('approvals.po.dashboard._general_info') --}}
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function() {
            console.log('ready');
        });
    </script>
@endsection
