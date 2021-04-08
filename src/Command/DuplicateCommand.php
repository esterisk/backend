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
		$record = $this->editor->record->replicate();
		$this->editor->form->fieldList['_id']->sourceId($id);
		$this->editor->form->options([ 'defaults' => $record, 'action' => $this->editor->commandRoute('save', $id), 'submit' => 'Inserisci' ]);

		$data = [
			'_id' => $id,
			'editor' => $this->editor,
			'form' => $this->editor->form,
			'panelid' => uniqid('panel'),
			'subtitle' => 'Nuovo',
		];
		return $this->view('esterisk/backend/edit', $data);
	}
	
}
