<?php
namespace Esterisk\Backend\ListCompiler;
use Illuminate\Http\Request;

class ResourceListCompiler
{
	var $resource;
	var $templateBase = 'esterisk/backend';
	var $info = null;

	public function __construct($resource, $model)
	{
		$this->resource = $resource;
		$this->model = $model;
		$this->init();
	}
	
	function init() {}
	
	public function rowFromRequest(Request $request)
	{
		$listing  = isset($request->listing) ? $request->listing : 'home';
		$id = isset($request->id) ? $request->id : null;
		$relationResource = isset($request->relationResource) ? $request->relationResource : null;
		$relationField = isset($request->relationField) ? $request->relationField : null;
		$relationId = isset($request->relationId) ? $request->relationId : null;

		return $this->row($listing, $id, $relationResource, $relationField, $relationId);
	}
	
	public function row($listing, $id, $relationResource, $relationField, $relationId)
	{
		$query = $this->listSetDefinition($info, $listing, $relationResource, $relationField, $relationId);
		if ($id) $record = $query->find($id);
		else $record = null;
		
		if (!$record) {
			return [
				'id' => $id,
				'html' => '',
				'count' => 0,
			];
		} else {
			$data = [
				'resource' => $this->resource,
				'listing' => $listing,
				'record' => $record,
			];
			return [
				'id' => $id,
				'html' => view($this->template($listing,'row'), $data)->render(),
				'count' => 1,
			];
		}
	}
	
	public function template($listing, $type)
	{
		return $this->templateBase.'/'.preg_replace('/[^a-zA-Z0-9_-]/','',$listing).'-listing/'.$type;
	}

	public function resourceHome(Request $request)
	{
		$this->addListSessionParameters($request);
		$query = $this->listSetDefinition($info,'home');
		$records = $query->paginate($this->resource->perPage);
		$info['rld']['fragtype'] = 'table';
		
		$data = [
			'resource'    => $this->resource,
			'records'     => $records,
			'currentPage' => $records->currentPage(),
			'perPage'     => $this->resource->perPage,
			'first'       => ($records->currentPage() - 1) * $this->resource->perPage + 1,
			'last'        => ($records->currentPage()) * $this->resource->perPage,
			'found'       => $info['count'],
			'total'       => $info['total'],
			'sort'        => $info['sorted'],
			'filters'     => $info['filters'],
			'tools'       => $info['tools'],
			'listing'     => 'home',
			'rld'	      => $info['rld'],
		];
		if ($data['last'] > $data['found']) $data['last'] = $data['found'];
		$data['rld']['page'] = $records->currentPage();

		return view('esterisk.backend.resource-home', $data);
	}
	
	public function tableFromRequest(Request $request)
	{
		$listing  = isset($request->listing) ? $request->listing : 'home';
		$page = isset($request->page) ? $request->page : null;
		$relationResource = isset($request->relationResource) ? $request->relationResource : null;
		$relationField = isset($request->relationField) ? $request->relationField : null;
		$relationId = isset($request->relationId) ? $request->relationId : null;

		return $this->table($listing, $page, $relationResource, $relationField, $relationId);
	}
	
	public function table($listing, $page, $relationResource, $relationField, $relationId)
	{
		$query = $this->listSetDefinition($info, $listing, $relationResource, $relationField, $relationId);
		$info['rld']['fragtype'] = 'table';

		if ($page !== null) {
			$records = $query->paginate($this->resource->perPage, ['*'], 'page', $page);
			$data = [
				'resource'    => $this->resource,
				'records'     => $records,
				'currentPage' => $records->currentPage(),
				'perPage'     => $this->resource->perPage,
				'first'       => ($records->currentPage() - 1) * $this->resource->perPage + 1,
				'last'        => ($records->currentPage()) * $this->resource->perPage,
				'listing'     => $listing,
				'rld'		  => $info['rld'],
			];
			$data['rld']['page'] = $records->currentPage();
		} else {
			$records = $query->get();
			$data = [
				'resource'    => $this->resource,
				'records'     => $records,
				'listing'     => $listing,
				'rld'		  => $info['rld'],
			];
		}
		return [
			'count' => $info['count'],
			'html' => view($this->template($listing,'table'), $data)->render(),
		];			
	}
	
	public function related($listing, $relationResource, $relationField, $relationId,$orderBy = null, $defaults = null)
	{
		$query = $this->listSetDefinition($info, $listing, $relationResource, $relationField, $relationId);
		if ($orderBy) $query = $query->orderBy(trim($orderBy,'-+'),(substr($orderBy,0,1)== '-' ? 'desc':'asc'));
		
		$info['rld']['fragtype'] = 'table';
		$defaultsQuery = '';
		$records = $query->get();
		if ($defaults) $defaultsQuery = '?'.http_build_query($defaults, '', '&amp;');
		
		$data = [
			'resource'    => $this->resource,
			'records'     => $records,
			'listing'     => $listing,
			'rld'		  => $info['rld'],
			'defaults'	  => $defaultsQuery,
		];

		return [
			'count' => $info['count'],
			'html' => view($this->template($listing,'wrap'), $data)->render(),
		];			
	}
	
	public function relatedTable($relationResource, $relationField, $relationId,$orderBy = null, $defaults = null)
	{
		return $this->related('related',$relationResource, $relationField, $relationId,$orderBy, $defaults);
	}
	
	/* List set definition */
	
	public function listSetDefinitionFromRequest(Request $request, &$info) {
		$listing  = isset($request->listing) ? $request->listing : 'home';
		$relationResource = isset($request->relationResource) ? $request->relationResource : null;
		$relationField = isset($request->relationField) ? $request->relationField : null;
		$relationId = isset($request->relationId) ? $request->relationId : null;
		return $this->listSetDefinition($info, $listing, $relationResource, $relationField, $relationId);
	}
	
	private function listSetDefinition(&$info, $listing, $relationResource = null, $relationField = null, $relationId = null)
	{
		$info = [ 'rld' => [ 
			'res' => $this->resource->slug,
			'rurl' => $this->resource->reloadRoute(), 
			'lstg' => $listing, 
		] ];
		if ($relationResource) {
			if (!($parentResource = $this->resource->otherResource($relationResource))) throw new \Exception('Parent resource not found');
			if (!$parentModel = $parentResource->model) throw new \Exception('Parent model not found');
			if (!$relationField || !($parentField = $parentResource->getRelationField($relationField))) throw new \Exception('Parent field not found');
			if (!$relationName = $parentField->relationName) throw new \Exception('Relation not found');
			if (!$relationId) throw new \Exception('Parent id not found');
			if (!$parent = $parentModel->find($relationId))  throw new \Exception('Parent object not found');
			$query = $parent->$relationName();
			
			$info['rld'] += [ 
				'relrs' => $relationResource,
				'relfd' => $relationField,
				'relid' => $relationId,
			];
		}
		else {
			$query = $this->resource->model;
		}
		$info['total'] = $query->count();
		
		if ($listing == 'home') {
			$listSession = $this->getListSession();
			$info['sorted'] = false;
			$info['filters'] = [];
			$info['tools'] = [ 'search' => '', 'searchIn' => '', 'selection' => '' ];
			foreach ($listSession as $key => $values) {
				switch ($key) {
			
					// ordinamento
					case 'sort': 
						if (!is_array($values)) $values = [ $values ];
						foreach ($values as $value) {
							$dir = substr($value,0,1) == '-' ? 'desc' : 'asc';
							$key = trim($value, '-');
							if ($key == 'id') $key = $this->resource->modelPrimaryKey;
							$query = $query->orderBy($key, $dir);
							$info['sorted'] = $value; 
							$info['filters'][] = [ 'icon' => $dir == 'asc' ? 'sort-down-icon' : 'sort-up-icon', 'label' => 'Ordinamento', 'key' => 'sort', 'description' => $this->resource->fieldLabel($key), 'value' => $value ];
						}
						break;
					
					// ricerca
					case 'search':
						if ($values) {
							if (!empty($listSession['searchIn']) && in_array($listSession['searchIn'], $this->resource->listFields)) $fields = [ $listSession['searchIn'] ];
							else $fields = array_merge(['id'], $this->resource->listFields);
							$pk = $this->resource->modelPrimaryKey;
							$query = $query->where(function($query) use ($pk, $values, $fields) {
								foreach ($fields as $field) {
									if ($field == 'id') $query = $query->orWhere($pk, '=', intval($values));
									$query = $query->orWhere($field, 'like', '%'.$values.'%');
								}
								return $query;
							});						
							$info['filters'][] = [ 'icon' => 'search-icon', 'label' => 'Ricerca', 'key' => 'search', 'description' => '«'.$values.'»', 'value' => $values ];
							$info['tools']['search'] = $listSession['search'];
							$info['tools']['searchIn'] = !empty($listSession['searchIn']) ? $listSession['searchIn'] : '';
						}
						break;
					
					// ricerca
					case 'selection':
						if ($values && ($closure = $this->resource->selections[$values]['query'])) {
							$query = $closure($query);						
							$info['filters'][] = [ 'icon' => 'funnel-icon', 'label' => 'Selezione', 'key' => 'selection', 'description' => $this->resource->selections[$values]['label'], 'value' => $values ];
							$info['tools']['selection'] = $listSession['selection'];
						}
						break;
				}
			}
			if (!$info['sorted']) $query = $query->orderBy($this->resource->modelPrimaryKey, 'desc');
		}
		$info['count'] = $query->count();
		return $query;
	}
	

	/* Session management */
	
	private function addListSessionParameters($request)
	{
		$addToSession = $request->all();
		if ($addToSession) $this->updateListSession($addToSession);
	}
	
	private function listSessionName()
	{
		return 'backendList'.$this->resource->slug;
	}
	
	private function getListSession()
	{
		return session($this->listSessionName(), []);
	}
		
	private function setListSession($data)
	{
		session($this->listSessionName(), []);
	}
		
	private function updateListSession($addToSession = [])
	{
		$listSession = $this->getListSession();
		foreach ($addToSession as $key => $value) if (in_array(trim($key, '+-!'), $this->resource->listQueryParameters)) {
			$sub = substr($key,0,1) == '-';
			$set = substr($key,0,1) == '!';
			$add = substr($key,0,1) == '+';
			$key = trim($key, '+-!');
			if (!$value) $sub = $key;

			if ($sub) { if (isset($listSession[$key])) {
				if (is_array($listSession[$key])) {
					$listSession[$key] = array_diff($listSession[$key], [ $value ]);
				} else {
					unset($listSession[$key]	);
				}
			}} else {
				if ($add || $set) {
					if ($add && isset($listSession[$key])) $listSession[$key][] = $value;
					else $listSession[$key] = [ $value ];
					$listSession[$key] = array_unique($listSession[$key]);
				} else {
					$listSession[$key] = $value;
				}
			}
		}
		session([ $this->listSessionName() => $listSession ]);
		return $listSession;
	}
	
	private function resetListSession()
	{
		session([ $this->listSessionName() => [] ]);
	}
	
}
