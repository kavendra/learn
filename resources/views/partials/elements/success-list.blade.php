{{-- Some views have access to $successes Collection --}}
@if( Session::has('successes') )
<ul class="list-unstyled">
    @foreach(Session::get('successes') as $message)
        <li>
            <div class="alert alert-success">
                <i class="fa fa-exclamation-circle"></i>
                {!! $message !!}
            </div>
        </li>
    @endforeach
</ul>
@endif
