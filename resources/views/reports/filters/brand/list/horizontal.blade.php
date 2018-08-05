{{--Filter by the Brand--}}
<div class="form-group">
  {{  Form::label('inBrand', 'Select Brand', ['class'=>'control-label col-sm-3 col-xs-12']) }}
  <div class="col-sm-9 col-xs-12">
    @forelse($inBrand as $brand)
      <div class="checkbox">
        <label>
          {{  Form::checkbox('inBrand[]', $brand->id, $brand->is_valid) }}
          {{ $brand->label }}

          @if($brand->is_inactive)
            <span class="label label-danger">Inactive</span>
          @endif

          @if($brand->is_not_valid)
            <span class="label label-warning" title="Brand is not current">Not Current</span>
          @endif
        </label>
      </div>
    @empty
      <p class="text-alert">You do not have access to any brands!</p>
    @endforelse
  </div>
</div>
