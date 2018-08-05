{{--Filter by the Ticket Categories--}}
<div class="form-group">
    {{  Form::label('ticket_category', 'Select Ticket Category:', ['class'=>'control-label col-sm-3 col-xs-12']) }}
    <div class="checkbox" id="categoryLabelDis">
        <label>
            <input type="checkbox" name="all_categories" id="all_categories" value="[{{ $ticketCategories->implode('id', ',') }}]" data-toggle="collapse" data-target=".all-categories" checked="checked"> All Categories
        </label>
    </div>
    <div class="all-categories collapse col-md-2">
        @foreach($ticketCategories as $category)
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('ticket_category[]', $category->id, true,['class'=>'ticket_category']) }} {{ $category->label }}
                </label>
            </div>
        @endforeach        
    </div>
</div>
