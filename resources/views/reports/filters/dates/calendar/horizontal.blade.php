{{--Year Range--}}
<div class="form-group">
  {{  Form::label('inYear', 'Calendar Year', ['class'=>'control-label col-sm-3'])  }}
  <div class="col-sm-3 col-md-2">
      <!--Display years in progression, last and current are selected-->
      {{  Form::selectRange('inYear', $yearRangeStart, $yearRangeEnd, $filterInYear , ['class'=>'form-control'])  }}
  </div>
</div>
