{{--From and To--}}
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      {{  Form::label('from') }}
      {{ Form::text('from',$filterFrom,array('class' => 'form-control datepicker')) }}
    </div>
  </div>
  <div class="col-xs-6">
    <div class="form-group">
      {{  Form::label('to') }}
      {{ Form::text('to',$filterTo,array('class' => 'form-control datepicker')) }}
    </div>
  </div>
</div>
