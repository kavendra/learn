{{--Filter by the Conference--}}
<div class="form-group">
  {{  Form::label('inConference', 'Select Conference', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-6">
    <!--Display years in progression, last and current are selected-->
    {{  Form::select('inConference', ['' => 'Please Select...'] + $inConference->pluck('label', 'id')->toArray(), null, ['class'=>'form-control', 'required'])  }}
  </div>
</div>

@if( $inConference->isEmpty() )
  <div class="alert-alert-danger">
    You do not have access to any Conferences!
  </div>
@endif
