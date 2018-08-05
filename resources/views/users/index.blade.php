@extends('layouts.app')
@section('title', '| Users')
@section('content')
<div class="col-lg-10 col-lg-offset-1">
    <h3><i class="fa fa-key"></i>User Management
        <a href="{{ URL::to('users/create') }}" class="btn btn-success pull-right btn-xs">Add User</a>
    </h3>
    <hr>
    <div class="table-responsive" id="user-pagination">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date/Time Added</th>
                    <!--th>User Roles</th-->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('F d, Y h:ia') }}</td>
                    <!--td>{{ $user->userRole->role->name or '' }} </td-->
                    <td>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-xs">Edit</a>
                    {{ Form::open(['route'=>['users.destroy',$user->id], 'method'=>'DELETE', 'style'=>'display:inline-block']) }}
                    {!!Form::token()!!}
                    <button type="submit" class="btn btn-xs btn-danger">
                        Delete
                    </button>  
                    {{ Form::close() }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            {{ $users->render() }}
        </table>
    </div>
</div>


<script type="text/javascript">

$(document).on('click','#user-pagination a', function(e){
        e.preventDefault();
        var url = $(this).attr('href');
        $("#user-pagination").load(url);
    });

</script>
@endsection

