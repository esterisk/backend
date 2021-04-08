@extends($editor->viewLayout)

@section('content')
<div class="list-tools row">

	<div class="col-sm">
		<form method="get">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><span class="search-icon"></span></span>
			</div>
			<input type="search" class="form-control" aria-label="Search" name="search" value="{{ !empty($tools['search']) ? $tools['search'] : '' }}">
			<div class="input-group-append">
				<select class="custom-select mr-sm-2" name="searchIn">
					<option value="">Tutti i campi</option>
					<option value="id"{{ $tools['searchIn']=='id' ? ' selected' : '' }}>ID</option>
					@foreach($editor->listFields as $key)
					<option value="{{ $key }}"{{ $tools['searchIn']==$key ? ' selected' : '' }}>{{ $editor->fieldLabel($key) }}</option>
					@endforeach
				</select>
			</div>
		</div>
		</form>
	</div>

	@if (!empty($editor->selections))
	<div class="col-sm">
		<form method="get">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><span class="funnel-icon"></span></span>
			</div>
			<select class="custom-select" name="selection" onchange="this.form.submit()">
				<option value="">{{ !empty($tools['selection']) ? 'Tutti' : 'Seleziona solo…' }}</option>
				@foreach($editor->selections as $key => $selection)
				<option value="{{ $key }}"{{ $tools['selection']==$key ? ' selected' : '' }}>{{ $selection['label'] }}</option>
				@endforeach
			</select>
		</div>
		</form>
	</div>
	@endif
	
</div>

@if($filters)
<div class="list-filters row"><div class="col-sm">
@foreach ($filters as $filter)
	<span class="list-filter">
		<acronym class="filter-icon {{ $filter['icon'] }}" title="{{ $filter['label'] }}"></acronym>
		{{ $filter['description'] }} 
		<a href="{{ $editor->listRoute([ '-'.$filter['key'] => $filter['value'] ]) }}" class="close">&times;</a>
		</span>
@endforeach
</div></div>
@endif

<div class="list-stats row">
	<div class="col-sm-4 ">
		@include('esterisk.backend.command-menu', [ 'cmdmenus' => $editor->editorCommandMenu() ])
	</div>
	<div class="col-sm-6 "><p style="line-height:37px">
		@if ($total == 0)
		Ancora nessun {{ $editor->singular() }}.
		@else
		<b>{{ $found }}</b> {{ $editor->plural() }} trovati su {{ $total }} totali ({{ round($found/$total*100, 2) }}%) - Mostrati da {{ $first }} a {{ $last }}
		@endif
	</p></div>
	<div class="col-sm-2">
	{{ $records->links() }}
	</div>
</div>

<div class="list-table row"><div class="col-sm">
@include('esterisk/backend/'.$listing.'-listing/table')
</div></div>

{{ $records->links() }}


<template id="panel-placeholder">
	<div class="backend-panel-wrap standby">
		<div class="container shadow-sm rounded-lg bg-white backend-panel clearfix">
		</div>
	</div>
</template>

@endsection

@section('templates')
<div class="modal fade" tabindex="-1" id="editor-modal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Editor</h5>
        <span class="modal-buttons float-right">
        
        </span>
      </div>
      <div class="modal-body">
        <p>Form non trovato.</p>
      </div>
    </div>
  </div>
</div>
@endsection


@section('jscript')
<script>

jQuery.fn.extend({
	getCommandOptions: function() {
		var id = $(this).data('command-select');
		$(this).data('command-url', $('[data-command-options='+id+'] option:selected').data('command-url'));
		$(this).data('command-method', $('[data-command-options='+id+'] option:selected').data('command-method'));
		$(this).data('command-title', $('[data-command-options='+id+'] option:selected').data('command-title'));
		$(this).data('command-confirm', $('[data-command-options='+id+'] option:selected').data('command-confirm'));
	}
});


$(document).ready(function(){

	$('.spotlight').removeClass('spotlight');

	$('.alert.feedback .close').click( function() {
		$('.alert.feedback').css('top','-100px');
	});
/*	
	$(document).on('click','button', function() {
		$('.modal-body button[data-action="'+$(this).data('action')+'"][data-mode="'+$(this).data('mode')+'"]').click();
		return false;
	});
*/	
	$(document).on('click','button[data-action="save-form"]', function() {
		$form = $(this).parents('form');
		var fdata = $form.gatherData('save');
		var after = ($(this).data('mode')!='save-continue') ? function() { $form.find('button[data-action="leave-editor"]').click(); } : null;
		
		let cmd = new Command($form.attr('action'),'multipart','save','','',fdata, after);
		cmd.execute();
		return false;
	});
	
	$(window).on('popstate', function(e) {
		$leave = $('#backend-stage .current button[data-action="leave-editor"]');
		if ($leave.length) {
			$leave.click();
			return false;
		} else return true;	
	});
	
	$(document).on('click','button[data-action="leave-editor"]',function() {
		console.log('chiesta chiusura');
		$form = $(this).parents('form');
		console.log($form);
		if (!$form.dirty() || confirm("Sei sicuro di voler chiudere? Le modifiche verranno perse") ) 	{
			console.log('chiudo');
			$form.stopDirty();
			$('#backend-stage').popPanel();
			return false;
		}
		else return false;
	});
	
	

	$(document).on('click','[data-command-url]', function() {
		if ($(this).data('command-select')) $(this).getCommandOptions();
	
		let cmd = new Command(
			$(this).data('command-url'),
			$(this).data('command-method'),
			$(this).data('command-title'),
			'#editor-modal',
			$(this).data('command-confirm'));
		return cmd.execute();
	});
});


$(document).ready(function() {
	$('.sort-button').click(function (event) {
		window.location = $(this).data('url');
		return false;
		event.preventDefault();
	});
});
function shiftHandler(event) {
    $('.backend-list').toggleClass('shift-pressed', event.shiftKey);
    $('.backend-list').toggleClass('alt-pressed', event.altKey);
};
window.addEventListener("keydown", shiftHandler, false);
window.addEventListener("keypress", shiftHandler, false);
window.addEventListener("keyup", shiftHandler, false);
</script>

@foreach($editor->scriptLibs as $lib)
<script src="{{ $lib }}"></script>
@endforeach
@endsection

