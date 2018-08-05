@extends('layouts.app')
@section('title', '| Projects')
@section('content')
<div class="container">
<h3><i class="fa fa-key"></i>Project Management
                @if(Auth::user()->hasRole('Admin') AND !empty($user))
                <a href="#remote" class="btn btn-success pull-right btn-xs" data-toggle="modal" data-remote="{{ route('user.projects.create', $user->id)}}"><i class="fa fa-plus"></i>Add Project</a>
                @else
                <a href="{{ route('projects.create') }}" class="btn btn-success pull-right btn-xs"><i class="fa fa-plus"></i>Add Project</a>
                @endif
            </h3>
            <hr>

    <div class="row">
    @if(isset($user) AND !empty($user))
     <div class="col-md-3">
            @include('partials.sidebar');
        </div>
    @endif    
        <div class="col-md-14">
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Project Name</th>
                            <th>Project Type</th>
                            <th>Project URL </th>
                            @if(empty($user))
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($projects->count() > 0)
                        @foreach ($projects as $project)
                        <tr>
                            <td>{{ $project->title }}</td>
                            <td>{{ $project->type->label or '' }}</td>
                            <td>{{ $project->url }}</td>
                            @if(empty($user))
                            <td>
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-xs">Edit</a>
                                {{ Form::open(['route'=>['projects.destroy',$project->id], 'method'=>'DELETE', 'style'=>'display:inline-block']) }}
                                {!!Form::token()!!}

                                <button type="submit" class="btn btn-xs btn-danger">
                                    Delete
                                </button> 
                              @endif   
                                {{ Form::close() }}

                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                        <td colspan="4">No Project found</td>
                        </tr>
                        @endif
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->

<div class="modal fade" id="{{ $id or 'remote'}}" data-backdrop="static">
  <div class="modal-dialog {{ $size or ''}}">
    <div class="modal-content">
      <div class="modal-content-loader">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Loading</h4>
        </div>
        <div class="modal-body">
          <div class="progress">
            <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 15%">
              <span class="sr-only">15% Complete</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection