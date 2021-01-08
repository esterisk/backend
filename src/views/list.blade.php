@extends(config('backend.backendViewLayout'))

@section('content')

<table class="table table-striped backend-list">
  <thead>
    <tr>
      <th><a href="{{ $backend->commandRoute('get','edit',$record->getKey()) }}?sort={{ $record->getKey() }}">ID</a></th>
@foreach($backend->listFields as $key)
      <th><a href="{{ $backend->commandRoute('get') }}?sort={{ $key }}">{{ $backend->fieldLabel($key) }}</a></th>
@endforeach
      <th>Comandi</th>
    </tr>
  </thead>
  <tbody>
@foreach($records as $record)
    <tr>
      <th class="field-id">{{ $record->getKey() }}</th>
@foreach($backend->listFields as $key)
      <td>{{ $record->$key }}</td>
@endforeach
	<td><a href="{{ $backend->commandRoute('get','edit',$record->getKey()) }}">Modifica</a></td>
    </tr>
@endforeach
  </tbody>
</table>

@endsection