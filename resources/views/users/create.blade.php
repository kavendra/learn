@extends('layouts.app')
@section('title', '| Create User')
@section('content')
<div class="container">
        <h3><i class='fa fa-user-plus'></i> Create User Information</h3>
        <hr>
    <div class="row">
    <div class="col-md-8">
        {!! Form::open(array('url' => 'users')) !!}
        <div class="form-group @if ($errors->has('name')) has-error @endif">
            {!! Form::label('name', 'Name') !!}
            {!! Form::text('name', '', array('class' => 'form-control')) !!}
        </div>
        <div class="form-group @if ($errors->has('email')) has-error @endif">
            {!! Form::label('email', 'Email') !!}
            {!! Form::email('email', '', array('class' => 'form-control')) !!}
        </div>
        <!--div class="form-group @if ($errors->has('roles')) has-error @endif">
            @foreach ($roles as $role)
            {!! Form::checkbox('roles[]',  $role->id ) !!}
            {!! Form::label($role->name, ucfirst($role->name)) !!}<br>
            @endforeach
        </div-->
        <div class="form-group @if ($errors->has('password')) has-error @endif">
            {!! Form::label('password', 'Password') !!}<br>
            {!! Form::password('password', array('class' => 'form-control')) !!}
        </div>
        <div class="form-group @if ($errors->has('password')) has-error @endif">
            {!! Form::label('password', 'Confirm Password') !!}<br>
            {!! Form::password('password_confirmation', array('class' => 'form-control')) !!}
        </div>
        {!! Form::submit('Register', array('class' => 'btn btn-primary')) !!}
        {!! Form::close() !!}
    </div>
</div>
</div>
@endsection