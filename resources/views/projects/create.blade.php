@extends('layouts.app')
@section('title', '| Create Project')
@section('content')
<div class="container">
    <h3><i class='fa fa-user-plus'></i> Create Project</h3>
    <hr>

    <div class="row">
    @if(!empty($user))
     <div class="col-md-4">
            @include('partials.sidebar');
        </div>
    @endif  
        <div class="col-md-8">
            {!! Form::open(array('route' => 'projects.store')) !!}
            <div class="form-group @if ($errors->has('title')) has-error @endif">
                {!! Form::label('title', 'Project Name') !!}
                {!! Form::text('title', '', array('class' => 'form-control')) !!}
            </div>
            <div class="form-group @if ($errors->has('type')) has-error @endif">
                {!! Form::label('type', 'Project Type') !!}
                {!! Form::select('type_id', $projectTypes,null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {!! Form::label('url', 'Project URL') !!}
                {!! Form::text('url', '', array('class' => 'form-control')) !!}
            </div>
            {!! Form::submit('Add', array('class' => 'btn btn-primary')) !!}
            {!! Form::close() !!}
        </div>
        </div>
    </div>
</div>
@endsection