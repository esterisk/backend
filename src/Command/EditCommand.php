<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class EditCommand extends Command
{
	var $label = 'Modifica';
	var $slug = 'edit';
	var $method = 'get';
	var $show = true;
	var $default = false;
	var $confirm = null;
	var $template = '#editor';
	var $icon = 'pencil-icon';

	public function execute($id = null, Request $request)
	{
		$this->resource->form->options([ 'defaults' => $this->resource->record, 'action' => $this->resource->commandRoute('save', $id) ]);

		$data = [
			'_id' => $id,
			'resource' => $this->resource,
			'form' => $this->resource->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Modifica',
		];

		return $this->view('esterisk/backend/edit', $data);
	}
	
}
