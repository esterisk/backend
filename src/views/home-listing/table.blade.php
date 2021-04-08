<table class="table table-striped backend-list" @foreach($rld as $d => $v) data-{{ $d }}="{{ $v }}" @endforeach>
  <thead>
    <tr>
      <th class="sortable text-right">
		<div class="sort-set">
			<button data-url="{{ $editor->listRoute([ '!sort' => 'id']) }}" class="btn btn-light btn-sm sort-button sort-alt"><i class="sort-down-icon"></i></button>
			<button data-url="{{ $editor->listRoute([ '!sort' => '-id']) }}" class="btn btn-light btn-sm sort-button sort-default"><i class="sort-up-icon"></i></button>
		</div>
		<div class="sort-add">
			<button data-url="{{ $editor->listRoute([ '+sort' => 'id']) }}" class="btn btn-light btn-sm sort-button sort-alt"><span>+</span><i class="sort-down-icon"></i></button>
			<button data-url="{{ $editor->listRoute([ '+sort' => '-id']) }}" class="btn btn-light btn-sm sort-button sort-default"><span>+</span><i class="sort-up-icon"></i></button>
		</div>
		ID
      </th>
@foreach($editor->listFields as $key)
      <th class="sortable{{ $editor->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">
		<div class="sort-set">
	      	<button data-url="{{ $editor->listRoute([ '!sort' => $key]) }}" class="btn btn-light btn-sm sort-button {{ $editor->fieldSortDirection($key) == 'asc' ? 'sort-default' : 'sort-alt' }}"><i class="sort-down-icon"></i></button>
			<button data-url="{{ $editor->listRoute([ '!sort' => '-'.$key]) }}" class="btn btn-light btn-sm sort-button {{ $editor->fieldSortDirection($key) == 'desc' ? 'sort-default' : 'sort-alt' }}"><i class="sort-up-icon"></i></button>
		</div>
		<div class="sort-add">
	      	<button data-url="{{ $editor->listRoute([ '+sort' => $key]) }}" class="btn btn-light btn-sm sort-button {{ $editor->fieldSortDirection($key) == 'asc' ? 'sort-default' : 'sort-alt' }}"><span>+</span><i class="sort-down-icon"></i></button>
    			<button data-url="{{ $editor->listRoute([ '+sort' => '-'.$key]) }}" class="btn btn-light btn-sm sort-button {{ $editor->fieldSortDirection($key) == 'desc' ? 'sort-default' : 'sort-alt' }}"><span>+</span><i class="sort-up-icon"></i></button>
		</div>
		{{ $editor->fieldLabel($key) }}
@endforeach
      <th style="position:relative">Comandi</th>
    </tr>
  </thead>
  <tbody>
@foreach($records as $record)
@include('esterisk/backend/home-listing/row')
@endforeach
  </tbody>
</table>
