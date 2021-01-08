<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class DeleteCommand extends Command
{
	var $label = 'Cancella';
	var $slug = 'delete';
	var $method = 'post';
	var $show = true;
	var $default = false;
	var $confirm = 'Sei sicuro di voler cancellare questo elemento? L’operazione non è annullabile.';
	var $icon = 'trash-icon';

	public function execute($id = null, Request $request)
	{
		if ($this->destroyModel($id)) {
		    return $this->success(ucfirst($this->resource->singular()).' '.$id.' cancellato', [ 
		    	'_id' => $id, 
		    	'resource' => $this->resource->slug, 
	         	'event' => $this->deletedEvent($id),
		    ]);
		} else {
		    return $this->error(ucfirst($this->resource->singular()).' '.$id.' non trovato');
		}
	}
		
	/* Working with Model */
	
	public function destroyModel($id)
	{
		$this->resource->record->delete();
		return true;
	}

}
