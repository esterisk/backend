<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class SaveCommand extends Command
{
	var $label = 'Salva';
	var $slug = 'save';
	var $method = 'post';
	var $show = false;
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
		$this->request = $request;
    	$this->resource->form->validate($request);
    	$data = $this->resource->form->salvable($request, $this->resource->record);
    	
    	if ($data['_id']) {
    		$record = $this->saveModel($data['_id'], $data);
    		$event = $this->modifiedEvent($record->getKey());
    	} else {
    		$record = $this->storeModel($data);
    		$event = $this->insertedEvent($record->getKey());
    	}
     	$this->afterSave($request, $record);
   	
    	$idfield = $this->resource->getField('_id')->render($id, $record);
    	
        return $this->success(ucfirst($this->resource->singular()).' '.$id.($this->resource->modelTitleField && !empty($data[$this->resource->modelTitleField]) ? ' «'.$data[$this->resource->modelTitleField].'»' : '').' salvato.', [
        	'id-field' => $idfield,
        	'reloaded-fields' => $this->resource->form->reloadFields($record),
        	'resource' => $this->resource->slug,
        	'event' => $event,
        	'_id' => $id 
        ]);
	}
		
	/* Working with Model */
	
	public function saveModel($id, $data)
	{
		unset($data['_id']);
		$record = $this->resource->model->find($id);
		foreach ($data as $key => $value) $record->$key = $value;
		$record->save();
		return $record;
	}
	
	public function storeModel($data)
	{
		unset($data['_id']);
		$record = new $this->resource->model;
		foreach ($data as $key => $value) $record->$key = $value;
		$record->save();
		return $record;
	}
	
	public function afterSave($request, $record)
	{
		$updates = $this->resource->form->afterSave($request, $record);
		if ($updates) {
			foreach ($updates as $key => $value) $record->$key = $value;
		}
		$record->save();
	}

}
