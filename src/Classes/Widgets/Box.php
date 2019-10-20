<?php

namespace Poppy\System\Classes\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Throwable;

class Box extends Widget implements Renderable
{

	/**
	 * @var string
	 */
	protected $title = '';

	/**
	 * @var string
	 */
	protected $content = 'here is the box content.';

	/**
	 * @var string
	 */
	protected $footer = '';

	/**
	 * @var array
	 */
	protected $tools = [];

	/**
	 * @var string
	 */
	protected $script;

	/**
	 * Box constructor.
	 *
	 * @param string $title
	 * @param string $content
	 */
	public function __construct($title = '', $content = '')
	{
		parent::__construct();
		if ($title) {
			$this->title($title);
		}

		if ($content) {
			$this->content($content);
		}
	}

	/**
	 * Set box content.
	 *
	 * @param string $content
	 *
	 * @return $this
	 */
	public function content($content): self
	{
		if ($content instanceof Renderable) {
			$this->content = $content->render();
		}
		else {
			$this->content = (string) $content;
		}

		return $this;
	}

	/**
	 * Set box title.
	 *
	 * @param string $title
	 *
	 * @return $this
	 */
	public function title($title): self
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Variables in view.
	 *
	 * @return array
	 */
	protected function variables(): array
	{
		return [
			'title'   => $this->title,
			'content' => $this->content,
			'tools'   => $this->tools,
		];
	}

	/**
	 * Render box.
	 */
	public function render()
	{
		try {
			return view('system::tpl.widgets.box', $this->variables())->render();
		} catch (Throwable $e) {
			return '';
		}
	}
}
