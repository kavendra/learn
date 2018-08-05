<!-- @unless( $history->isEmpty() )
    <p class="lead">{{ $title or 'Recent Reports' }}</p>
    <ul class="list-unstyled">
        @foreach( $history as $reportHistory)
            <li id="report-history-id-{{ $reportHistory->id }}">
                <p>
                    <i class="fa fa-file-excel-o"></i>
                    {{ $reportHistory->link }}
                    <em class="text-muted">{{ $reportHistory->created_at->diffForHumans() }}</em>
                </p>
            </li>
        @endforeach
    </ul>
@endif
 -->