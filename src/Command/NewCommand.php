<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class NewCommand extends Command
{
	var $label = '+ Nuovo %s';
	var $slug = 'edit';
	var $method = 'get';
	var $show = true;
	var $target = 'resource';
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
	    $this->resource->form->options([ 'action' => $this->resource->commandRoute('save', $id), 'submit' => 'Inserisci' ]);
	    $this->resource->form->defaultsFromRequest($request);

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
