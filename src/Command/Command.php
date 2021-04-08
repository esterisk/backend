<?php
namespace Esterisk\Backend\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Esterisk\Form\Form;

class Command
{
	var $label;
	var $slug;
	var $editor;
	var $method = 'get';
	var $show = true;
	var $target = 'record';
	var $default = false;
	var $confirm = null;
	var $template = null;
	var $icon = null;

	public function __construct($slug, $editor)
	{
		$this->slug = $slug;
		$this->editor = $editor;
		$this->label = str_replace('%s', $this->editor->singular(), str_replace('%p', $this->editor->plural(), $this->label));
		$this->init();
	}
	
	public function __get($property) {
		if (property_exists($this, $property)) {
			if (method_exists($this, $method = 'get'.ucfirst($property))) return $this->$method();
			return $this->$property;
		}
	}

	public function __set($property, $value) {
		if (property_exists($this, $property)) {
			if (method_exists($this, $method = 'set'.ucfirst($property))) return $this->$method($value);
			$this->$property = $value;
		}
		return $this;
	}
	
	public function __call($property, $value) {
		if (!count($value)) $value = [ 1 ];
		if (property_exists($this, $property) || property_exists($this, $property = snake_case($property))) {
			if (method_exists($this, $method = 'set'.ucfirst($property))) return $this->$method($value);
			$this->$property = $value[0];
		}
		return $this;
	}
	
	public function execute($id = null, Request $request)
	{
	}

	public function back($status, $message, $info = null)
	{
		return $this->editor->back($status, $message, $info);
	}
	
	public function error($message, $info = null)
	{
		return $this->back('error', $message, $info);
	}
	
	public function success($message, $info = null) 
	{
		return $this->back('success', $message, $info);
	}
		
	public function warning($message, $info = null) 
	{
		return $this->back('warning', $message, $info);
	}

	public function route($id = null)
	{
		return $this->editor->commandRoute($this->slug, $id);
	}
	
	public function view($view, $data)
	{
		if (request()->wantsJson() || request()->has('json')) {
			$data['layout'] = $this->editor->panelLayout;
			$data['json'] = true;
			$result = [ 
				'template' => $this->template, 
				'id' => $data['_id'], 
				'html' => view($view, $data)->render(),
				'subtitle' => isset($data['subtitle']) ? $data['subtitle'] : '',
				'panelid' => isset($data['panelid']) ? $data['panelid'] : uniqid('panel'),
			];
			return response()->json($result);
		} else {
			$data['layout'] = $this->editor->viewLayout;
			$data['json'] = true;
			return view($view, $data);
		}
	}
	
	/* Create Events */
	
	public function backendEvent($event, $id, $data = [])
	{
		return array_merge($data, [ 'event' => $event, 'editor' => $this->editor->slug, 'id' => $id ]);
	}
	
	public function modifiedEvent($id)
	{
		return $this->backendEvent('modified',$id);
	}
	
	public function deletedEvent($id)
	{
		return $this->backendEvent('deleted',$id);
	}
	
	public function insertedEvent($id)
	{
		return $this->backendEvent('inserted',$id);
	}
	
	public function detachedEvent($id)
	{
		return $this->backendEvent('detached',$id);
	}
	
	

}
