@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase order / dashboard
    </small>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">

            <x-proc-po-links page="dashboard" />


            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
@endsection
