<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class DetachCommand extends Command
{
	var $label = 'Rimuovi';
	var $slug = 'detach';
	var $method = 'post';
	var $show = true;
	var $default = false;

	public function execute($id = null, Request $request)
	{
		if ($this->detachModel($id)) {
		    return $this->success(ucfirst($this->editor->singular()).' '.$id.' rimosso', [ 
		    	'_id' => $id, 
		    	'editor' => $this->editor->slug, 
	         	'event' => $this->detachedEvent($id),
		    ]);
		} else {
		    return $this->error(ucfirst($this->editor->singular()).' '.$id.' non trovato');
		}
	}
		
	/* Working with Model */
	
	public function detachModel($id)
	{
//		$this->editor->record->delete();
		return true;
	}

}
