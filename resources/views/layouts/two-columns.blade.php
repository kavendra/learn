@extends('layouts.app')

@section('body')
    @parent
    <div class="container-fluid">
        <header class="page-header no-margin">

            @unless(app()->environment('production'))
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    You are in <strong>{{ strtoupper(app()->environment()) }}</strong> environment.
                </div>
            @endif

            @section('breadcrumbs')
                <nav>
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/') }}" rel="boormark"><i class="fa fa-home"></i></a></li>
                        @show
                    </ol>
            </nav>

            @section('header')<h1>@show</h1>

        </header>
        <!--/Page Header-->

        <div id="page-content">
            <aside class="sidebar">
                @yield('sidebar')
            </aside>

            <article class="content">
              <div class="row">
                <div class="col-xs-12">

                  @include('partials.elements.error-list')
                  @include('partials.elements.message-list')
                  @include('partials.elements.success-list')

                  @yield('content')

                </div>
              </div>
            </article>
        </div>

        <!--Additional Scripts -->
        <aside>
          @section('scripts')
          @show
        </aside>

    </div>

@stop
