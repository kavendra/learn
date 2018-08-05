{{--Year Range--}}
<div class="row">
  {{  Form::label('inYear', 'Calendar Year')  }}
  <!--Display years in progression, last and current are selected-->
  {{  Form::selectCalendarYear('inYear', $yearRangeStart, $yearRangeEnd, $filterInYear , ['class'=>'form-control'])  }}
</div>
