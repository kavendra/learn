<ul class="list-unstyled list-expanded list-hover">
 
    <li>
      <div class="pull-right">
        {!! Form::open(['route'=>'reports.transaction.list-report.index']) !!}
          <button class="btn btn-default btn-hover-warning hidden">
            <i class="fa fa-download-alt"></i>
            <span class="hidden-xs">Download</span>
          </button>
        {!! Form::close() !!}
      </div>
      <i class="fa fa-filter"></i>
      {{ link_to_route( 'reports.transaction.list-report.index', 'Conference List Report') }}
      <p class="text-muted">Information about conferences.</p>
    </li>
  

</ul>
