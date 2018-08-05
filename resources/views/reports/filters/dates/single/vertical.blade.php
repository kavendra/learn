{{--Single Date--}}
<div class="row">
  <div class="col-xs-6">
    <div class="form-group">
      {{  Form::label('at', 'Pick Date') }}
      {{ Form::text('at',$filterAt,array('class' => 'form-control', 'data-provide'=>'datepicker')) }}
    </div>
  </div>
</div>
