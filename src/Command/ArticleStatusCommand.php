<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class ArticleStatusCommand extends Command
{
	var $label = 'Imposta status';
	var $slug = 'article-status';
	var $method = 'post';
	var $show = false;
	var $default = false;
	var $confirm = null;
	var $return = 'result';
	var $field = 'status';
	var $updateTime = null;
	var $updateDate = null;
	var $updateWhen = 'today';

	public function execute($id = null, Request $request)
	{
		$fieldName = $this->field;
		if (!$fieldName) return $this->error('Campo errato');
		if (!$request->has($fieldName) || !($status = $request->$fieldName)) return $this->error('Valore non indicato');
		$field = $this->editor->getField($fieldName);
		if (empty($field->statuses)) return $this->error('Campo errato');
		if (empty($field->statuses[$status])) return $this->error('Stato non previsto');
		
		$this->editor->record->$fieldName = $field->prepareForSave($status);
		if ($status == 'published') {
			if ($updateTime = $this->updateTime) $this->editor->record->$updateTime = date('Y-d-m H:i:s');
			if ($updateDate = $this->updateDate) {
				switch ($this->updateWhen) {
					case 'tomorrow': $date = strtotime('+1 day'); break;
					case 'today': default: $date = time(); break;
				}
				$this->editor->record->$updateDate = date('Y-m-d', $date);
			}
		}
		$this->editor->record->save();
		
		return $this->success($this->editor->singular().' '.$id.' impostato su '.$field->labels[$status], [
         	'event' => $this->modifiedEvent($id),
        	'_id' => $id 
        ]);
	}
	
	public function today($dateField) 
	{
		$this->updateDate = $dateField;
		$this->updateWhen = 'today';
		return $this;
	}
	
	public function tomorrow($dateField) 
	{
		$this->updateDate = $dateField;
		$this->updateWhen = 'tomorrow';
		return $this;
	}
		
}
