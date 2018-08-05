{{--Filter by the Ticket Statuses--}}
<div class="form-group">
    {{  Form::label('ticketStatuses', 'Select Ticket Status:' , ['class'=>'control-label col-sm-3 col-xs-12']) }}
    <div class="checkbox" id="statusLabelDis">
        <label>
            <input type="checkbox" name="all_statuses" id="all_statuses" value="[{{ $ticketStatuses->implode('id', ',') }}]" data-toggle="collapse" data-target=".all-statuses" checked="checked"> All Statuses
        </label>
    </div>
    <div class="all-statuses collapse col-md-2">
        @foreach($ticketStatuses as $status)
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('ticket_status[]', $status->id, true,['class'=>'ticket_status']) }} {{ $status->label   }}
                </label>
            </div>
        @endforeach        
    </div>
</div>
