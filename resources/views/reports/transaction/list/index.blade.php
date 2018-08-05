@extends('reports.transaction.layout')

@section('breadcrumbs')
    @parent
    <li class="active">Transaction List</li>
@stop

@section('header')
  @parent
  <small class="tab-header">Transaction List Report</small>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-7">

            <p class="lead">Review {{ trans('app.label') }} Conferences</p>
            <p class="help-block">List all conferences, or filter them by date.</p>
            {!!  Form::open(['class'=>'form-horizontal']) !!}

                @include('reports.transaction.list.form')

                <div class="form-actions form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        {!!  Form::submit('Download Report',['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
            {!!  Form::close()!!}
        </div>
        <div class="col-lg-7">
            @include('reports.recent', ['title'=>'My Recent Transaction List Reports'])
      </div>
    </div>
@stop
