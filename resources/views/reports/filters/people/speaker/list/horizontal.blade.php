<div class="form-group">
  {{  Form::label('speakers[]', 'Limit by Speakers:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-9 col-xs-12">
    <div class="checkbox">
      <label>
        <input type="checkbox" checked id="toggle_all_speakers" data-toggle="collapse" data-target="#speaker-list"> Report on all Speakers
      </label>
    </div>
  </div>
</div>
<div id="speaker-list" class="collapse">
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9 col-xs-12">
      {{  Form::select( 'speakers[]'
                      , $speakerList
                      , null
                      , [ 'class'=>'form-control', 'multiple', 'disabled', 'size'=> 10 ])}}
      <p class="help-block">Visible are all speakers in brands, {{ $user->profile->preferred_name }} has access to. </p>
      <p class="help-block">To select multuiple speakers, hold the <var>Ctrl</var> on your keyboard key while clicking on the names.</p>
    </div>
  </div>
</div>
<script>
$("#toggle_all_speakers").change(function(){
  $("select[name^='speakers']").attr("disabled", $(this).is(':checked'));
});
</script>
