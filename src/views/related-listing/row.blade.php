	<tr {!! $resource->spotlight($record) ? 'class="spotlight"' : '' !!} data-fragtype="row" data-resid="{{ $record->getKey() }}">
		<th class="field-id text-right">{{ $record->getKey() }}</th>
@foreach($resource->relatedListFields as $key)
	    <td class="{{ $resource->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">{!! $resource->fieldShow($key, $record) !!}</td>
@endforeach
		<td style="width:12em" class="text-right">
			<a class="btn btn-sm btn-outline-secondary" data-command-title="Modifica" data-command-url="{{ $resource->commandRoute('edit',$record->getKey()) }}" data-command-method="get" title="Modifica"><span class="command-icon pencil-icon"></span></a>
			<a class="btn btn-sm btn-outline-secondary" data-command-title="Rimuovi" data-command-url="{{ $resource->commandRoute('delete',$record->getKey()) }}" data-command-method="post" title="Rimuovi"><span class="command-icon trash-icon"></span></a>
		</td>
	</tr>