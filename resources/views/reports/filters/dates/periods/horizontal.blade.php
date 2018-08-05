{{--From and To--}}
<div class="form-group">
  {{  Form::label('inPeriod', 'Period:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-3 col-xs-6">
    {{  Form::text('from', $filterFrom, ['class'=>' form-control','data-provide'=>'datepicker']) }}
  </div>
  <div class="col-sm-3 col-xs-6">
    {{  Form::text('to', $filterTo, ['class'=>'form-control', 'data-provide'=>'datepicker']) }}
  </div>
  <div class="col-sm-3">&nbsp;</div>
</div>
