  <div class="form-group">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      {{ Form::label('label', 'Projects') }}
    </div>
    <div class="col-xs-6 col-sm-6 col-md-3 col-lg-6">
      {{ Form::select('project_id', ['' => 'Select Project'] + $projects, null, array('class' => 'form-control', 'required' => 'required')) }}
    </div>
  </div>
  </div>

