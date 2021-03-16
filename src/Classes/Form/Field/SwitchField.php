<?php

namespace Poppy\System\Classes\Form\Field;

use Poppy\System\Classes\Form\Field;

class SwitchField extends Field
{

	protected $default = 0;

	public function render()
	{
		$this->addVariables([
			'options' => [
				0 => '关闭',
				1 => '开启',
			],
		]);
		return parent::render();
	}
}
