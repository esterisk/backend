<?php
namespace Esterisk\Backend;

use Illuminate\Support\Facades\Route;

class BackendRouter
{
	public static function doRoutes()
	{
		$backends = config('backend.backends');
		$domains = [];
		$subdirs = [];
		
		foreach ($backends as $label => $backend) {
			
			if ($backend['urlPosition'] == 'domain') $domains[] = $label;
			else $subdirs[] = $label;
		
			if (count($domains)) {
				Route::domain('{backend}')
					->middleware(isset($backend[$label]['middleware']) ? $backend[$label]['middleware'] : (isset($backend['middleware']) ? $backend['middleware'] : 'web'))
					->group(function() use ($domains) { BackendRouter::createRoutes($domains); });
			}
			
		
			if (count($subdirs)) {
				Route::prefix('/{backend}')
					->middleware(isset($backend[$label]['middleware']) ? $backend[$label]['middleware'] : (isset($backend['middleware']) ? $backend['middleware'] : 'web'))
					->group(function() use ($subdirs) { BackendRouter::createRoutes($subdirs); });
			}
		
		}
	}

	private static function createRoutes($group)
	{	
		Route::get('/',
			'Esterisk\Backend\Http\Controllers\BackendController@dashboard')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.index');

		Route::any('/{resource}/lookup/{field}',
			'Esterisk\Backend\Http\Controllers\BackendController@lookupRouter')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.lookup');

		Route::any('/{resource}/ajax/rld',
			'Esterisk\Backend\Http\Controllers\BackendController@reloadRouter')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.reload');

		Route::post('/{resource}/ajax/{cmd}/{id?}',
			'Esterisk\Backend\Http\Controllers\BackendController@ajaxRouter')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.ajax');

		Route::get('/{resource}/{cmd?}/{id?}/{other?}',
			'Esterisk\Backend\Http\Controllers\BackendController@getRouter')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.get');

		Route::post('/{resource}/{cmd}/{id?}',
			'Esterisk\Backend\Http\Controllers\BackendController@postRouter')
				->where('backend', implode('|',$group))
				->name('esterisk.backend.post');
	}

}
