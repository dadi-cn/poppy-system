<?php namespace Poppy\System\Classes\Form\Field;

class DateRange extends Date
{
	/**
	 * {@inheritdoc}
	 */
	public function render()
	{
		$this->options([
			'range' => true,
		]);
		return parent::render();
	}
}
