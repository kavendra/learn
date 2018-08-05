<h1 class="page-header">Query Log</h1>

 @if($queries = DB::getQueryLog())
  <table class="table-striped table-hover">
    <thead>
      <tr>
        <th class="id"></th>
        <th class="date-short"></th>
        <th></th>
        <th class="status"></th>
      </tr>
    </thead>
    <tbody>

    @foreach($queries as $id => $query)
      <tr>
        <td valign="top">{!!  $id !!}</td>
        <td valign="top">{!! array_get($query,'time') !!}</td>
        <td valign="top">
          <div class="collapse .masked">{!! array_get($query,'query') !!}</div>
          <div class="text-muted" data-toggle="collapse" data-target=".masked">{!!  @vsprintf(str_replace('?', '%s', array_get($query,'query')), array_get($query,'bindings',[]))  !!}</div>
        </td>
        <td>{!! implode(', ', array_get($query,'bindings',[])) !!}</td>
      </tr>
    @endforeach

    </tbody>
  </table>
 @endif
