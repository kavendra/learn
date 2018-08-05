@extends('layouts.app')
@section('title', '| Project')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="well">
                <h4> Development Projects </h4><hr>
                @if($getUser->develop)
                @foreach($getUser->develop as $project)
                <ul class="list-unstyled">
                    <li><a href="lsp.dev"> {{ $project->title or '' }} </a></li>
                </ul>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="well">
                <h4> UAT Projects </h4><hr>
                @if($getUser->uat_develop)
                @foreach($getUser->uat_develop as $project)
                <ul class="list-unstyled">
                    <li><a href="{{ $project->url }}" target="__blank"> {{ $project->title or '' }} </a></li>
                </ul>
                @endforeach
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="well">
                <h4> Production Projects </h4><hr>
                @if($getUser->production_develop)
                @foreach($getUser->production_develop as $project)
                <ul class="list-unstyled">
                    <li><a href="lsp.dev"> {{ $project->title or '' }} </a></li>
                </ul>
                @endforeach
                @endif
            </div>
        </div>
       
        
        <!--div class="col-md-4">
            <div class="well">
                <h3> UAT Projects </h3>
                <ul class="list-unstyled">
                    <li><a href="uat-lsp.com"> LSP UAT </a></li>
                    <li><a href="uat-hcsp.com"> Horizon UAT </a></li>
                    <li><a href="lsp.dev"> Aries UAT </a></li>   
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="well">
                <h3> Production Projects </h3>
                <ul class="list-unstyled">
                    
                </ul>
            </div>
        </div-->
    </div>
</div>
@endsection
