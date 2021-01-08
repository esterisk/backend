<?php
namespace Esterisk\Backend;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Validator;
use Esterisk\Form\Form;

class Resource
{
	var $commands;
	var $commandsMenu;
	var $resourceCommandsMenu;
	var $itemName = [ 'elemento', 'elementi' ];
	var $backendTitle = null;
	var $modelClass;
	var $modelPrimaryKey;
	var $modelTitleField = false;
	var $record = null; 
	var $perPage = 10;
	var $listQueryParameters = [ 'sort', 'search', 'selection', 'searchIn' ];
	var $section = '';
	var $priority = '';
	var $backend = null; 
	var $viewLayout = '';
	var $panelLayout = '';
	var $slug = null;
	var $foreColor = "inherit";
	var $backColor = "inherit";
	var $defaultCommands = [ 'list', 'edit', 'duplicate', 'delete', 'save', 'new' ];
	var $relatedCommands = [ 'edit', 'detach' ];
	var $scriptLibs = [];
	var $listCompilerClass = \Esterisk\Backend\ListCompiler\ResourceListCompiler::class;
	var $listCompiler = null;

	public function __construct($backend, $slug)
	{
		$this->slug = $slug;
		$this->backend($backend);
		$this->addDefaultCommands();
		$this->init($this->createForm());
		$this->model = new $this->modelClass;
	}
	
	public function init($form)
	{
	}
	
	public function addDefaultCommands()
	{
		foreach ($this->defaultCommands as $key) $this->addCommands([ $this->command($key), ]);
	}
	
	public function createForm()
	{
		$this->form = new Form();
		$this->form->resource($this);
		$this->form->action($this->commandRoute('save'))
			->method('post')
			->addFields([
				$this->form->field('id', '_id' )->label('ID')
			]);
		return $this->form;
	}
	
	public function activateDirty($activate = true)
	{
		$this->form->fieldList['_id']->activateDirty($activate);
	}
	
	public function backend($backend = null)
	{
		if (!$backend) return $this->backend;
		else {
			$this->backend = $backend;
			$this->viewLayout = $backend->config('viewLayout');
			$this->panelLayout = $backend->config('panelLayout');
		}
	}
	
	public function otherResource($slug)
	{
		return $this->backend->resource($slug);
	}
	
	/* COMMANDS */
	
	public function command($slug, $handlerClass = null)
	{
		if (!$handlerClass) $handlerClass = 'Esterisk\\Backend\\Command\\'.ucfirst(camel_case($slug)).'Command';
		$handler = new $handlerClass($slug, $this);
		return $handler;
	}

	public function addCommands($commands) {
		foreach ($commands as $handler) {
			$this->commands[$handler->slug] = $handler;
			if ($handler->show && $handler->target == 'record') $this->commandsMenu[]= $handler->slug;
			if ($handler->show && $handler->target == 'resource') $this->resourceCommandsMenu[]= $handler->slug;
		}
	}

	public function rmCommands($slugs) {
		if (!is_array($slugs)) $slugs = [ $slugs ];
		foreach ($slugs as $slug) {
			unset($this->commands[$slug]);
		}
	}

	public function execute($slug, $id, $request)
	{
		if ($id) {
			if ($this->record = $this->model->find($id)) {
				$this->record->_id = $id;
			} else {
				return $this->back('error',$this->singular().' '.$id.' non trovato');
			}
		}
		if (!isset($this->commands[$slug])) throw new \Exception('Comando non trovato');
		if (method_exists($this, ($method = ucfirst(camel_case($slug)).'Command'))) return $this->$method($id, $request);
		else {
			return $this->commands[$slug]->execute($id, $request);
		}
	}

	public function lookup($fieldname, $request)
	{
		if (!$field = $this->getField($fieldname)) throw new \Exception('Campo non trovato');
		if (!method_exists($field, 'executeLookup')) throw new \Exception('Campo non valido');
		return response()->json($field->executeLookup($request));
	}

	/* Returns tables, table rows and fields html */
	public function reload($request)
	{
		switch ($request->fragmentType) {
			
			case 'table':
				return response()->json($this->listCompiler()->tableFromRequest($request));
			break;
			
			case 'row':
				return $this->listCompiler()->rowFromRequest($request);
			break;
			
			case 'field':
			break;	
		
		}
	}
	
	public function commandMenu($id = null)
	{
		$menu = [];
		if (count($this->commandsMenu)) foreach ($this->commandsMenu as $slug) {
			$link = $this->commandInfo($slug, $id);
			$menu[$link['url']] = $link;
		}
		return $menu;
	}
	
	public function resourceCommandMenu()
	{
		$menu = [];
		if (count($this->resourceCommandsMenu)) foreach ($this->resourceCommandsMenu as $slug) {
			$link = $this->commandInfo($slug);
			$menu[$link['url']] = $link;
		}
		return $menu;
	}
	
	public function commandInfo($slug, $id = null)
	{
		if (isset($this->commands[$slug])) return [ 
			'url' => $this->commandRoute($slug, $id), 
			'label' => $this->commands[$slug]->label, 
			'method' => $this->commands[$slug]->method,
			'confirm' => $this->commands[$slug]->confirm,
			'icon' => $this->commands[$slug]->icon 
		];
		else return null;
	}

	public function back($status, $message, $info = null)
	{
		$result = [ 'status' => $status, 'message' => ucfirst($message) ];
		if ($info) $result = array_merge($result, $info);
		
		if (request()->wantsJson() || request()->has('json')) {
			return response()->json($result);
    	} else {
		    $this->backend->statusSession($result);
    	    return redirect($this->listRoute());
    	}
	}

	/* Interfaccia */
	
	public function listCompiler()
	{
		if (!$this->listCompiler) $this->listCompiler = new $this->listCompilerClass($this, $this->model);
		return $this->listCompiler;
	}
	
	/* called from the parent resource */
	public function relatedList($relationResource, $relationField, $relationId,$orderBy = null, $defaults = null)
	{
		return $this->otherResource($relationResource)->childList($this->slug, $relationField, $relationId,$orderBy, $defaults);
	}
	
	/* called from the related resorce */
	public function childList($parentResource, $relationField, $relationId,$orderBy = null, $defaults = null)
	{
		return $this->listCompiler()->relatedTable($parentResource, $relationField, $relationId, $orderBy, $defaults);
	}

	public function pageTitle()
	{
		return $this->backendTitle ?: ucfirst($this->plural());
	}
	
	public function newTitle()
	{
		return $this->pageTitle();
	}
	
	public function singular() { return $this->itemName[0]; }
	public function plural() { return $this->itemName[1]; }
	public function backgroundColor() { return $this->backColor; }
	public function foregroundColor() { return $this->foreColor; }
	
	/* Route */
	
	public function myslug()
	{
		return strtolower(basename(str_replace('Backend','',str_replace('\\','/',get_class($this)))));
	}
	
	public function baseRoute()
	{
		return $this->listRoute();
	}
	
	public function listRoute($addToSession = null)
	{
		return $this->backend->resourceRoute($this->slug, $addToSession);
	}
	
	public function listRowRoute($id, $listing = null)
	{
		return $this->backend->route('get', $this->slug, 'list', $id, [ 'listing' => $listing ]);
	}
	
	public function commandRoute($command = null, $id = null)
	{
		return $this->backend->route($this->commands[$command]->method, $this->slug, $command, $id);
	}
	
	public function ajaxRoute($command = null, $id = null)
	{
		return $this->backend->ajaxRroute($this->slug, $command, $id);
	}
	
	public function lookupRoute($field)
	{
		return $this->backend->lookupRoute($this->slug, $field);
	}
	
	public function reloadRoute()
	{
		return $this->backend->reloadRoute($this->slug);
	}
	
/* FORM AND DATA ACCESS */

	public function getField($key)
	{
		if (isset($this->form->fieldList[$key])) return $this->form->fieldList[$key];
		else return new \Esterisk\Form\Field\FieldText('dummy',$this->form);
	}

	public function getRelationField($key)
	{
		if (isset($this->form->relationFieldList[$key])) return $this->form->relationFieldList[$key];
		else return new \Esterisk\Form\Field\FieldText('dummy',$this->form);
	}

	public function fieldLabel($key)
	{
		if ($key == 'id') return 'ID';
		return $this->getField($key)->label;
	}
	
	public function fieldSortDirection($key)
	{
		return $this->getField($key)->sortDirection;
	}
	
	public function fieldShowAlign($key)
	{
		return $this->getField($key)->showAlign;
	}
	
	public function fieldShow($key,&$record)
	{
		if ($method = method_exists($this, 'fieldShow'.ucfirst(camel_case($key)))) return $this->$method($record);
		else {
			$field = $this->getField($key);
			$field->record($record);
			return $field->show($record->$key);
		}
	}
	
	public function spotlight($record)
	{
		if (isset($record->updated_at)) {
			$diff = time() - strtotime($record->updated_at);
			return ($diff < 10);
		} else return false;
	}

}
