
{{ Form::open(['route'=>array('user.projects.store', $user->id)]) }}
{!! Form::hidden('user_id', $user ? $user->id : null, array('class' => 'form-control')) !!}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">
            Assign Project
        </h4>
    </div>
    <div class="modal-body">
          @include('user-project.form')
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
{{ Form:: close() }}
