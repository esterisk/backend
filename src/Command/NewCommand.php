<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class NewCommand extends Command
{
	var $label = '+ Nuovo %s';
	var $slug = 'edit';
	var $method = 'get';
	var $show = true;
	var $target = 'editor';
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
	    $this->editor->form->options([ 'action' => $this->editor->commandRoute('save', $id), 'submit' => 'Inserisci' ]);
	    $this->editor->form->defaultsFromRequest($request);

		$data = [
			'_id' => $id,
			'editor' => $this->editor,
			'form' => $this->editor->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Modifica',
		];

		return $this->view('esterisk/backend/edit', $data);
	}
	
}
