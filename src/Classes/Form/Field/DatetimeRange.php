<?php namespace Poppy\System\Classes\Form\Field;

class DatetimeRange extends Datetime
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
