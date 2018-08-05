<div class="form-group">
  <div class="checkbox">
    <label>
      <input type="checkbox" checked id="toggle_all_speakers"> Report on all speakers, or:
    </label>
  </div>
</div>
<div class="form-group">
  {{  Form::label('speakers[]','Select Speaker') }}
  {{  Form::select( 'speakers[]', $speakerListDropdown , null, [ 'class'=>'form-control', 'multiple', 'disabled', 'size'=> 10 ])}}

  <p class="help-block">Visible are all speakers in brands, {{ $user->profile->preferred_name }} has access to. </p>
  <p class="help-block">To select multuiple speakers, hold the <var>Ctrl</var> on your keyboard key while clicking on the names.</p>
</div>

<script>
$("#toggle_all_speakers").change(function(){
  $("select[name^=speakers]").attr("disabled", $(this).is(':checked'));
});
</script>
