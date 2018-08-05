{{--Filter by the Brand--}}
<div class="form-group">
  {{  Form::label('inBrand','Select Brand') }}
  {{  Form::select('inBrand', $inBrand->lists('label',' id'), null, ['class'=>'form-control'])  }}
</div>

@if( $inBrand->isEmpty() )
  <div class="alert-alert-danger">
    You do not have access to any brands!
  </div>
@endif
