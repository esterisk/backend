<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class ExportCommand extends Command
{
	var $label = 'Esporta';
	var $slug = 'export';
	var $method = 'post';
	var $show = true;
	var $target = 'editor';
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
	    $this->editor->form->options([ 'action' => $this->editor->commandRoute('save', $id), 'submit' => 'Inserisci' ]);
	
		$data = [
			'_id' => null,
			'editor' => $this->editor,
			'form' => $this->editor->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Nuovo',
		];
		return $this->view('esterisk/backend/form', $data);
	}
	
}
