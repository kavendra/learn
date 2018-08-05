{{-- Every view has access to $errors Collection --}}
@unless(empty($errors) OR $errors->isEmpty() )
<ul class="list-unstyled">
    @foreach($errors->all() as $message)
        <li>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i>
                {!! $message !!}
            </div>
        </li>
    @endforeach
</ul>
@endunless
