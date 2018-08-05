@extends('layouts.two-columns')

@section('breadcrumbs')
    @parent
    <li><a href="{{ route('reports') }}" rel="boormark">Reports</a></li>
@stop

@section('sidebar')
    @unless( empty($sidebar) )
        {!! $sidebar->asUl(['class'=>'nav nav-list nav-sidebar']) !!}
    @else
        @parent
    @endunless
@stop
