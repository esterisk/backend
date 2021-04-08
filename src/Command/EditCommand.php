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
		$this->editor->form->options([ 'defaults' => $this->editor->record, 'action' => $this->editor->commandRoute('save', $id) ]);
		foreach ($this->editor->form->relations as $relation) {
			$this->editor->form->setRelationDefaults($relation, $this->editor->getRelationDefaults($relation));
		}
		
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
