<?php

namespace Poppy\System\Classes\Form\Field;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Poppy\System\Classes\Form\Field;

class Text extends Field
{
	use PlainInput;

	/**
	 * @var string
	 */
	protected $icon = 'fa-pencil';

	/**
	 * Set custom fa-icon.
	 *
	 * @param string $icon
	 *
	 * @return $this
	 */
	public function icon($icon)
	{
		$this->icon = $icon;

		return $this;
	}

	/**
	 * Render this filed.
	 *
	 * @return Factory|View
	 */
	public function render()
	{
		$this->initPlainInput();

		$this->prepend('<i class="fa ' . $this->icon . ' fa-fw"></i>')
			->defaultAttribute('id', $this->id)
			->defaultAttribute('class', 'layui-input ' . $this->getElementClassString())
			->defaultAttribute('placeholder', $this->getPlaceholder());

		$this->addVariables([
			'prepend' => $this->prepend,
			'append'  => $this->append,
			'type'    => 'text',
		]);

		return parent::render();
	}

	/**
	 * Add inputmask to an elements.
	 *
	 * @param array $options
	 *
	 * @return $this
	 */
	public function inputmask($options)
	{
		$options = json_encode_options($options);

		$this->script = "$('{$this->getElementClassSelector()}').inputmask($options);";

		return $this;
	}

	/**
	 * Add datalist element to Text input.
	 *
	 * @param array $entries
	 *
	 * @return $this
	 */
	public function datalist($entries = [])
	{
		$this->defaultAttribute('list', "list-{$this->id}");

		$datalist = "<datalist id=\"list-{$this->id}\">";
		foreach ($entries as $k => $v) {
			$datalist .= "<option value=\"{$k}\">{$v}</option>";
		}
		$datalist .= '</datalist>';

		return $this->append($datalist);
	}
}
