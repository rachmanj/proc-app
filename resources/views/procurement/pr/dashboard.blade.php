@extends('layout.main')

@section('title_page')
    Purchase Request
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase request / dashboard
    </small>
@endsection



@section('content')
    <div class="row">
        <div class="col-12">

            <x-proc-pr-links page="dashboard" />


            <div class="card">
                <div class="card-body">

                </div>
            </div>
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
