<?php
namespace Esterisk\Backend;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Validator;
use Esterisk\Form\Form;

class Backend
{
	var $backends = [];
	var $sections = [];
	var $siteTitle = 'Backend';
	var $prefix = '';
	var $config;

	public function __construct($prefix)
	{
		$this->loadConfig($prefix);
		$this->init();
	}
	
	public function init()
	{
	}
	
	public function loadConfig($prefix)
	{
		$this->prefix = $prefix;
		$this->config = config('backend.backends.'.$this->prefix);
		$this->viewLayout = $this->config['viewLayout'];
	}
	
	public function config($prefix)
	{
		return array_get($this->config, $prefix);
	}
	
	public function prefix() { return $this->prefix; }

	public function statusSessionLabel() { return $this->prefix.'-status'; }
	
	public function statusSession($status = null) 
	{
		if (!$status) return request()->session()->flash($this->statusSessionLabel());
		else request()->session()->flash($this->statusSessionLabel(), $status);

	}
	
	public function registerSection($section, $priority = 99)
	{
		$this->sections[] = [ 'name' => $section, 'priority' => $priority ];
	}

	public function register($slug, $resourceClass, $name, $section = null, $priority = null)
	{
		$this->resources[$slug] = [
			'class' => $resourceClass, 
			'name' => $name,
			'route' => $this->resourceRoute($slug), 
			'section' => $section ?: '', 
			'priority' => $priority ?: '99' 
		];
	}
	
	/* Create routes */
	
	public function route($kind, $resource, $cmd = null, $id = null, $addToSession = [])
	{
		if ($addToSession == null) $addToSession = [];
		return route('esterisk.backend.'.strtolower($kind), 
			array_merge([ 'backend' => $this->prefix, 'resource' => $resource, 'cmd' => $cmd, 'id' => $id ], $addToSession));
	}
	
	public function resourceRoute($resource, $addToSession = [])
	{
		return $this->route('get', $resource, null, null, $addToSession);
	}
	
	public function getRoute($resource, $cmd = null, $id = null)
	{
		return $this->route('get', $resource, $cmd, $id);
	}
	
	public function postRoute($resource, $cmd = null, $id = null)
	{
		return $this->route('post', $resource, $cmd, $id);
	}
	
	public function ajaxRoute($resource, $cmd = null, $id = null)
	{
		return $this->route('ajax', $resource, $cmd, $id);
	}
	
	public function reloadRoute($resource)
	{
		return route('esterisk.backend.reload', 
			[ 'backend' => $this->prefix, 'resource' => $resource ]);
	}
	
	public function lookupRoute($resource, $field)
	{
		return route('esterisk.backend.lookup', 
			[ 'backend' => $this->prefix, 'resource' => $resource, 'field' => $field ]);
	}
	
	/* Execute commands */
	
	public function execute($resource, $command, $id, $request)
	{
		return $this->resource($resource)->execute($command, $id, $request);
	}
	
	public function resource($resource)
	{
		if (empty($this->resources[$resource])) throw new \Exception('Backend resource not found');
		$class = $this->resources[$resource]['class'];
		return new $class($this, $resource);
	}
	
	/* Execute lookup */
	
	public function lookup($resource, $field, $request)
	{
		return $this->resource($resource)->lookup($field, $request);
	}
	
	/* Execute reload */
	
	public function reload($resource, $request)
	{
		return $this->resource($resource)->reload($request);
	}
	
	
	private function sortResources(&$array)
	{
		usort($array, function($a, $b) {
			if ($a['section'] != $b['section']) {
				return ($a['section'] < $b['section']) ? -1 : 1;
			}
			if ($a['priority'] != $b['priority']) {
				return ($a['priority'] < $b['priority']) ? -1 : 1;
			}
			if ($a['name'] == $b['name']) return 0;
			return ($a['name'] < $b['name']) ? -1 : 1;
		});
	}
	
	
	
	public function resourcesMenu()
	{
		$this->sortResources($this->resources);
		
		/* qui inserire verifica ruolo e accessibilità */
		$sections = [];
		foreach ($this->resources as $resource) {
			$sections[$resource['section']][] = $resource;
		}
		
		return $sections;
	}

}
