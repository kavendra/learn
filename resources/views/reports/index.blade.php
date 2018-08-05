@extends('reports.layout')

@section('breadcrumbs')
    @parent
    <li class="active">All Reports</li>
@stop

@section('header')
    @parent
    Reports Now
@stop

@section('content')
    <div class="row">
        <div class="col-lg-7">List</div>
        <div class="col-lg-5">Latest History</div>
    </div>
@stop
