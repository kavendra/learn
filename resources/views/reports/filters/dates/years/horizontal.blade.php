{{--Year Range--}}
<div class="row">
  {{  Form::label('inYear[]', 'Select Year', ['class'=>'control-label col-sm-3 col-xs-12'])  }}
  <div class="col-sm-4 col-xs-12">
    <div class="form-group">
      <!--Display years in progression, last and current are selected-->
      {{  Form::selectYear('inYear[]', $yearRangeStart, $yearRangeEnd, $filterInYear , ['class'=>'form-control','multiple'=>'multiple'])  }}
    </div>
  </div>
</div>

<div class="help-block">To select multiple years, hold Ctrl key on your keyboard while making selection.</div>
