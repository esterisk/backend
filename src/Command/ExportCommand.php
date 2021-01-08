<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class ExportCommand extends Command
{
	var $label = 'Esporta';
	var $slug = 'export';
	var $method = 'post';
	var $show = true;
	var $target = 'resource';
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
	    $this->resource->form->options([ 'action' => $this->resource->commandRoute('save', $id), 'submit' => 'Inserisci' ]);
	
		$data = [
			'_id' => null,
			'resource' => $this->resource,
			'form' => $this->resource->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Nuovo',
		];
		return $this->view('esterisk/backend/form', $data);
	}
	
}
