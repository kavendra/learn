{{-- Some views have access to $messages Collection --}}
@unless(empty($messages) OR $messages->isEmpty() )
<ul class="list-unstyled">
    @foreach($messages->all() as $message)
        <li>
            <div class="alert alert-info">
                <i class="fa fa-exclamation-circle"></i>
                {!! $message !!}
            </div>
        </li>
    @endforeach
</ul>
@endunless
