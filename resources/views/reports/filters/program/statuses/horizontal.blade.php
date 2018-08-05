<div class="form-group">
  {{  Form::label('inStatus', 'Program Status:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-9 col-xs-12">
    <div class="checkbox">
      <label>
        <input type="checkbox" checked data-toggle="collapse" data-target="#programStatus"> All
      </label>
    </div>
    <div id="programStatus" class="collapse">
      @forelse($programStatuses as $status)
        <div class="checkbox">
          <label>
            {{  Form::checkbox('inStatus[]', $status->id, true) }}
            {{ $status->label }}
            {{ $status->description ? "<em class='text-muted'><small>{$status->description}</small></em>" : '' }}
          </label>
        </div>
      @empty
        <p class="text-alert">There appears to be no statuses for the programs... Error!</p>
      @endforelse
    </div>
  </div>
</div>
