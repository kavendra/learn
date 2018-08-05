{{--Status of the Program--}}
<div class="form-group">
  {{  Form::label('programStatusesDropdown','Select Program Status')  }}
  @forelse($programStatuses as $status)
    <div class="checkbox">
      <label>
        <input type="checkbox" name="inStatus[]" value="{{ $status->id }}" checked="checked">
        {{ $status->label }}
      </label>
    </div>
  @empty
  <p class="text-danger">Error: There are no program statuses!</p>
  @endforelse
</div>
