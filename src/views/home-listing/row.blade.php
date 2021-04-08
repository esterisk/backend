	<tr {!! $editor->spotlight($record) ? 'class="spotlight"' : '' !!} data-fragtype="row" data-resid="{{ $record->getKey() }}">
		<th class="field-id text-right">{{ $record->getKey() }}</th>
@foreach($editor->listFields as $key)
	    <td class="{{ $editor->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">{!! $editor->fieldShow($key, $record) !!}</td>
@endforeach
		<td style="width:12em">
			<form method="get" onsubmit="this.action=$(this).find('select').val()">
				@include('esterisk.backend.command-menu', [ 'cmdmenus' => $editor->commandMenu($record->getKey()), 'record' => $record ])
			</form>
		</td>
	</tr>