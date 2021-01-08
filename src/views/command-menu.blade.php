@if(count(array_keys($cmdmenus)) > 1)
			<div class="input-group">
				<select class="browser-default custom-select" @if(isset($record)) data-command-options="{{ $record->getKey() }}" @else data-command-options="global" @endif>
@foreach($cmdmenus as $url => $menu)
					<option value="{{ $url }}" 
						data-command-title="{{ $menu['label'] }}" 
						data-command-url="{{ $menu['url'] }}" 
						data-command-method="{{ $menu['method'] }}"
						data-command-confirm="{{ $menu['confirm'] }}" 
						>{!! $menu['icon'] ? '<span class"command-icon '.$menu['icon'].'"></span>' : '' !!}{{ $menu['label'] }}</option>
@endforeach
				</select>
				<div class="input-group-append">
					<button class="btn btn btn-primary" @if(isset($record)) data-command-select="{{ $record->getKey() }}" @else data-command-select="global" @endif data-command-title="" data-command-url="" data-command-method="" data-command-confirm="" type="button"><i class="go-icon"></i></button>
				</div>
			</div>
@elseif(count(array_keys($cmdmenus))==1)
	@foreach($cmdmenus as $url => $menu)
			<button class="btn btn btn-primary" 
				data-command-title="{{ $menu['label'] }}" 
				data-command-url="{{ $menu['url'] }}" 
				data-command-method="{{ $menu['method'] }}" 
				data-command-confirm="{{ $menu['confirm'] }}" 
				type="button">{{ $menu['icon'] ? '<span class"command-icon '.$menu['icon'].'"></span>' : '' }}{{ $menu['label'] }}</button>
	@endforeach
@else
@endif
