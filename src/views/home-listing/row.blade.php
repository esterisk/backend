	<tr {!! $resource->spotlight($record) ? 'class="spotlight"' : '' !!} data-fragtype="row" data-resid="{{ $record->getKey() }}">
		<th class="field-id text-right">{{ $record->getKey() }}</th>
@foreach($resource->listFields as $key)
	    <td class="{{ $resource->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">{!! $resource->fieldShow($key, $record) !!}</td>
@endforeach
		<td style="width:12em">
			<form method="get" onsubmit="this.action=$(this).find('select').val()">
				@include('esterisk.backend.command-menu', [ 'cmdmenus' => $resource->commandMenu($record->getKey()), 'record' => $record ])
			</form>
		</td>
	</tr>