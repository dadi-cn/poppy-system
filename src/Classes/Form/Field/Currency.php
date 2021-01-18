<?php namespace Poppy\System\Classes\Form\Field;

class Currency extends Text
{

	/**
	 * {@inheritdoc}
	 */
	public function prepare($value)
	{
		return (float) $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function render()
	{
		$this->defaultAttribute('style', 'width: 120px');
		$this->addVariables([
			'type' => 'number',
		]);
		return parent::render();
	}
}
