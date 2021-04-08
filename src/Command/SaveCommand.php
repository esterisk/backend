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
    	$this->editor->form->validate($request);
    	$data = $this->editor->form->salvable($request, $this->editor->record);
    	
    	if ($data['_id']) {
    		$record = $this->saveModel($data['_id'], $data);
    		$event = $this->modifiedEvent($record->getKey());
    	} else {
    		$record = $this->storeModel($data);
    		$event = $this->insertedEvent($record->getKey());
    	}
    	
    	if ($this->editor->form->relations) foreach ($this->editor->form->relations as $relationName) $this->editor->saveRelation($relationName);
     	$this->afterSave($request, $record);
   	
    	$idfield = $this->editor->getField('_id')->render($id, $record);
    	
        return $this->success(ucfirst($this->editor->singular()).' '.$id.($this->editor->modelTitleField && !empty($data[$this->editor->modelTitleField]) ? ' «'.$data[$this->editor->modelTitleField].'»' : '').' salvato.', [
        	'id-field' => $idfield,
        	'reloaded-fields' => $this->editor->form->reloadFields($record),
        	'editor' => $this->editor->slug,
        	'event' => $event,
        	'_id' => $id 
        ]);
	}
		
	/* Working with Model */
	
	public function saveModel($id, $data)
	{
		unset($data['_id']);
		$record = $this->editor->model->find($id);
		foreach ($data as $key => $value) $record->$key = $value;
		$record->save();
		return $record;
	}
	
	public function storeModel($data)
	{
		unset($data['_id']);
		$record = new $this->editor->model;
		foreach ($data as $key => $value) $record->$key = $value;
		$record->save();
		return $record;
	}
	
	public function afterSave($request, $record)
	{
		$updates = $this->editor->form->afterSave($request, $record);
		if ($updates) {
			foreach ($updates as $key => $value) $record->$key = $value;
		}
		$record->save();
	}

}
