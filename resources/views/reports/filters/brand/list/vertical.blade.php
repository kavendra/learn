{{--Filter by the Brand--}}
<div class="form-group">
    {{  Form::label('Brands', 'Select Brand:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
    <div class="checkbox" id="brandLabelDis">
        <label>
            <input type="checkbox" name="all_brands" id="all_brands" value="[{{ $inBrand->implode('id', ',') }}]" data-toggle="collapse" data-target=".all-brands" checked="checked"> All Brands
        </label>
    </div>
    <div class="all-brands collapse col-md-2">
        @forelse($inBrand as $brand)
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('inBrand[]', $brand->id, true,['class'=>'']) }} 
                    {{ $brand->label  }}
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