<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class DuplicateCommand extends Command
{
	var $label = 'Duplica';
	var $slug = 'duplicate';
	var $method = 'get';
	var $show = true;
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
		$record = $this->resource->record->replicate();
		$this->resource->form->fieldList['_id']->sourceId($id);
		$this->resource->form->options([ 'defaults' => $record, 'action' => $this->resource->commandRoute('save', $id), 'submit' => 'Inserisci' ]);

		$data = [
			'_id' => $id,
			'resource' => $this->resource,
			'form' => $this->resource->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Nuovo',
		];
		return $this->view('esterisk/backend/edit', $data);
	}
	
}
