{{--Single Date--}}
<div class="form-group">
  {{  Form::label('at', 'Pick Date', ['class'=>'control-label col-xs-3']) }}
  <div class="col-xs-6 form-inline">
      {{ Form::text('at', $filterAt, array('class' => 'form-control', 'data-provide'=>'datepicker')) }}
  </div>
</div>
