	<tr {!! $editor->spotlight($record) ? 'class="spotlight"' : '' !!} data-fragtype="row" data-resid="{{ $record->getKey() }}">
		<th class="field-id text-right">{{ $record->getKey() }}</th>
@foreach($editor->relatedListFields as $key)
	    <td class="{{ $editor->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">{!! $editor->fieldShow($key, $record) !!}</td>
@endforeach
		<td style="width:12em" class="text-right">
			<a class="btn btn-sm btn-outline-secondary" data-command-title="Modifica" data-command-url="{{ $editor->commandRoute('edit',$record->getKey()) }}" data-command-method="get" title="Modifica"><span class="command-icon pencil-icon"></span></a>
			<a class="btn btn-sm btn-outline-secondary" data-command-title="Rimuovi" data-command-url="{{ $editor->commandRoute('delete',$record->getKey()) }}" data-command-method="post" title="Rimuovi"><span class="command-icon trash-icon"></span></a>
		</td>
	</tr>