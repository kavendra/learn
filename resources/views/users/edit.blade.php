@extends('layouts.app')
@section('title', '| Update User')
@section('content')
<div class="container">
    <h3><i class='fa fa-user-plus'></i> Update User Information</h3>
    <hr>
    <div class="row">
        <div class="col-md-4">
            @include('partials.sidebar');
        </div>
        <div class="col-md-8">
    {{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'PUT')) }}
    <div class="form-group @if ($errors->has('name')) has-error @endif">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', null, array('class' => 'form-control')) }}
    </div>
    <div class="form-group @if ($errors->has('email')) has-error @endif">
        {{ Form::label('email', 'Email') }}
        {{ Form::email('email', null, array('class' => 'form-control')) }}
    </div>
    <?php /*?>
    <!-- <h5><b>Assign Role</b></h5>
    <div class="form-group @if ($errors->has('roles')) has-error @endif">
        @foreach ($roles as $role)
            {{ Form::checkbox('roles[]',  $role->id, $user->roles ) }}
            {{ Form::label($role->name, ucfirst($role->name)) }}<br>
        @endforeach
    </div>
    <div class="form-group @if ($errors->has('password')) has-error @endif">
        {{ Form::label('password', 'Password') }}<br>
        {{ Form::password('password', array('class' => 'form-control')) }}
    </div>
    <div class="form-group @if ($errors->has('password')) has-error @endif">
        {{ Form::label('password', 'Confirm Password') }}<br>
        {{ Form::password('password_confirmation', array('class' => 'form-control')) }}
    </div> -->
    <?php */ ?>
    {{ Form::submit('Update', array('class' => 'btn btn-primary btn-xs')) }}
    {{ Form::close() }}
</div>
    </div>
</div>

@endsection