<div class="form-group">
  {{  Form::label('reps[]', 'Representatives:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-9 col-xs-12">
    <div class="checkbox">
      <label>
        <input type="checkbox" checked id="toggle_all_reps" data-toggle="collapse" data-target="#rep-list"> Report on all Field Users
      </label>
    </div>
  </div>
</div>
<div id="rep-list" class="collapse">
  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-9 col-xs-12">
      {{  Form::select('reps[]', $fieldList, null, [ 'class'=>'form-control', 'multiple', 'disabled', 'size'=> 10 ])}}
      <p class="help-block">Visible are all field users.</p>
      <p class="help-block">To select multuiple records, hold the <var>Ctrl</var> on your keyboard key while clicking on the names.</p>
    </div>
  </div>
</div>

<script>
$("#toggle_all_reps").change(function(){
  $("select[name^='reps']").attr("disabled", $(this).is(':checked'));
});
</script>
