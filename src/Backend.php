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

	public function register($slug, $editorClass, $name, $section = null, $priority = null)
	{
		$this->editors[$slug] = [
			'class' => $editorClass, 
			'name' => $name,
			'route' => $this->editorRoute($slug), 
			'section' => $section ?: '', 
			'priority' => $priority ?: '99' 
		];
	}
	
	/* Create routes */
	
	public function route($kind, $editor, $cmd = null, $id = null, $addToSession = [])
	{
		if ($addToSession == null) $addToSession = [];
		return route('esterisk.backend.'.strtolower($kind), 
			array_merge([ 'backend' => $this->prefix, 'editor' => $editor, 'cmd' => $cmd, 'id' => $id ], $addToSession));
	}
	
	public function editorRoute($editor, $addToSession = [])
	{
		return $this->route('get', $editor, null, null, $addToSession);
	}
	
	public function getRoute($editor, $cmd = null, $id = null)
	{
		return $this->route('get', $editor, $cmd, $id);
	}
	
	public function postRoute($editor, $cmd = null, $id = null)
	{
		return $this->route('post', $editor, $cmd, $id);
	}
	
	public function ajaxRoute($editor, $cmd = null, $id = null)
	{
		return $this->route('ajax', $editor, $cmd, $id);
	}
	
	public function reloadRoute($editor)
	{
		return route('esterisk.backend.reload', 
			[ 'backend' => $this->prefix, 'editor' => $editor ]);
	}
	
	public function lookupRoute($editor, $field)
	{
		return route('esterisk.backend.lookup', 
			[ 'backend' => $this->prefix, 'editor' => $editor, 'field' => $field ]);
	}
	
	/* Execute commands */
	
	public function execute($editor, $command, $id, $request)
	{
		return $this->editor($editor)->execute($command, $id, $request);
	}
	
	public function editor($editor)
	{
		if (empty($this->editors[$editor])) throw new \Exception('Backend editor not found');
		$class = $this->editors[$editor]['class'];
		return new $class($this, $editor);
	}
	
	/* Execute lookup */
	
	public function lookup($editor, $field, $request)
	{
		return $this->editor($editor)->lookup($field, $request);
	}
	
	/* Execute reload */
	
	public function reload($editor, $request)
	{
		return $this->editor($editor)->reload($request);
	}
	
	
	private function sortEditors(&$array)
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
	
	
	
	public function editorsMenu()
	{
		$this->sortEditors($this->editors);
		
		/* qui inserire verifica ruolo e accessibilità */
		$sections = [];
		foreach ($this->editors as $editor) {
			$sections[$editor['section']][] = $editor;
		}
		
		return $sections;
	}

}
