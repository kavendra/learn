{{--Filter by the Brand--}}
<div class="form-group">
  {{  Form::label('inBrand', 'Select Brand', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-6">
    <!--Display years in progression, last and current are selected-->
    {{  Form::select('inBrand', $inBrand->pluck('label', 'id'), null, ['class'=>'form-control'])  }}
  </div>
</div>

@if( $inBrand->isEmpty() )
  <div class="alert-alert-danger">
    You do not have access to any brands!
  </div>
@endif
