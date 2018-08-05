@extends('reports.transaction.layout')

@section('breadcrumbs')
    @parent
    <li class="active">All</li>
@stop

@section('header')
  @parent
  <small class="tab-header">Transaction  Reports</small>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-7">
            @include('reports.transaction.all')
        </div>
        <div class="col-lg-7">
            @include('reports.recent', ['title'=>'My Recent Transaction Reports'])
      </div>
    </div>
@stop
