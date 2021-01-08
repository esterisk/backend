<table class="table table-striped backend-list" @foreach($rld as $d => $v) data-{{ $d }}="{{ $v }}" @endforeach>
  <thead>
    <tr>
      <th class="sortable text-right">
		<div class="sort-set">
			<button data-url="{{ $resource->listRoute([ '!sort' => 'id']) }}" class="btn btn-light btn-sm sort-button sort-alt"><i class="sort-down-icon"></i></button>
			<button data-url="{{ $resource->listRoute([ '!sort' => '-id']) }}" class="btn btn-light btn-sm sort-button sort-default"><i class="sort-up-icon"></i></button>
		</div>
		<div class="sort-add">
			<button data-url="{{ $resource->listRoute([ '+sort' => 'id']) }}" class="btn btn-light btn-sm sort-button sort-alt"><span>+</span><i class="sort-down-icon"></i></button>
			<button data-url="{{ $resource->listRoute([ '+sort' => '-id']) }}" class="btn btn-light btn-sm sort-button sort-default"><span>+</span><i class="sort-up-icon"></i></button>
		</div>
		ID
      </th>
@foreach($resource->listFields as $key)
      <th class="sortable{{ $resource->fieldShowAlign($key) == 'right' ? ' text-right' : '' }}">
		<div class="sort-set">
	      	<button data-url="{{ $resource->listRoute([ '!sort' => $key]) }}" class="btn btn-light btn-sm sort-button {{ $resource->fieldSortDirection($key) == 'asc' ? 'sort-default' : 'sort-alt' }}"><i class="sort-down-icon"></i></button>
			<button data-url="{{ $resource->listRoute([ '!sort' => '-'.$key]) }}" class="btn btn-light btn-sm sort-button {{ $resource->fieldSortDirection($key) == 'desc' ? 'sort-default' : 'sort-alt' }}"><i class="sort-up-icon"></i></button>
		</div>
		<div class="sort-add">
	      	<button data-url="{{ $resource->listRoute([ '+sort' => $key]) }}" class="btn btn-light btn-sm sort-button {{ $resource->fieldSortDirection($key) == 'asc' ? 'sort-default' : 'sort-alt' }}"><span>+</span><i class="sort-down-icon"></i></button>
    			<button data-url="{{ $resource->listRoute([ '+sort' => '-'.$key]) }}" class="btn btn-light btn-sm sort-button {{ $resource->fieldSortDirection($key) == 'desc' ? 'sort-default' : 'sort-alt' }}"><span>+</span><i class="sort-up-icon"></i></button>
		</div>
		{{ $resource->fieldLabel($key) }}
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
