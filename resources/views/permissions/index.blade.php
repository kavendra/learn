@extends('layouts.app')
@section('title', '| Permissions')
@section('content')
<div class="col-md-10 col-md-offset-1">
    <h1><i class="fa fa-key"></i>Permissions Management
    <a href="{{ URL::to('permissions/create') }}" class="btn btn-success">Add Permission</a>
    </h1>
    <hr>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td> 
                    <td>
                    <a href="{{ URL::to('permissions/'.$permission->id.'/edit') }}" class="btn btn-warning btn-xs">Edit</a>
                    {{ Form::open(['route'=>['permissions.destroy',$permission->id], 'method'=>'DELETE', 'style'=>'display:inline-block']) }}
                    {!!Form::token()!!}
                    
                    <button type="submit" class="btn btn-xs btn-danger">
                        Delete
                    </button>  
                    {{ Form::close() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection