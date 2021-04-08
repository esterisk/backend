<?php
namespace Esterisk\Backend\Command;
use Illuminate\Http\Request;

class ListCommand extends Command
{
	var $label = 'Torna all\'elenco';
	var $slug = '';
	var $method = 'get';
	var $show = false;
	var $default = false;
	var $confirm = null;

	public function execute($id = null, Request $request)
	{
		if (!$id) return $this->editor->listCompiler()->editorHome($request);
		else return $this->editor->listCompiler()->row($id, $request->list);
	}
}
