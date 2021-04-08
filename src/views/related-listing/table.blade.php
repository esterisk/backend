@if(!$records->count())
<div class="alert alert-light border" role="alert" @foreach($rld as $d => $v) data-{{ $d }}="{{ $v }}" @endforeach>
  Ancora nessun elemento collegato.
</div>
@else

<table class="table table-striped backend-list border" @foreach($rld as $d => $v) data-{{ $d }}="{{ $v }}" @endforeach>
  <thead>
    <tr>
      <th class="text-right">
		ID
      </th>
@foreach($editor->relatedListFields as $key)
      <th class="{{ $editor->fieldShowAlign($key) == 'right' ? 'text-right' : '' }}">
		{{ $editor->fieldLabel($key) }}
@endforeach
      <th class="text-right">Comandi</th>
    </tr>
  </thead>
  <tbody>
@foreach($records as $record)
@include('esterisk/backend/related-listing/row')
@endforeach
  </tbody>
</table>

@endif