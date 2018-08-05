@extends('layouts.app')
@section('title', '| Edit Project')
@section('content')
<div class="container">
    <h3><i class='fa fa-user-plus'></i> Edit Project</h3>
    <hr>
    <div class="row">
        <div class="col-md-8">
       
            {!! Form::model($project, array('route' => ['projects.update',$project->id], 'method'=>'PUT')) !!}
            <div class="form-group @if ($errors->has('title')) has-error @endif">
                {!! Form::label('title', 'Project Name') !!}
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
            </div>
            <div class="form-group @if ($errors->has('type')) has-error @endif">
                {!! Form::label('type', 'Project Type') !!}
                {!! Form::select('type_id', $projectTypes, null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group @if ($errors->has('url')) has-error @endif">
                {!! Form::label('url', 'Project URL') !!}
                {!! Form::text('url', null, array('class' => 'form-control')) !!}
            </div>
            {!! Form::submit('Update', array('class' => 'btn btn-primary')) !!}
            {!! Form::close() !!}
        </div>
        </div>
    </div>
</div>
@endsection