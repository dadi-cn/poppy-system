<?php

namespace Poppy\System\Classes\Forms;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Poppy\Core\Classes\Traits\CoreTrait;
use Poppy\System\Classes\Widgets\Form;

class Setting extends Form
{

	use CoreTrait;



	public function setModule($module)
	{
		$definition = $this->coreModule()->pages()->offsetGet($module);
	}
	/**
	 * The form title.
	 *
	 * @var string
	 */
	public $title = 'Settings';

	/**
	 * Handle the form request.
	 *
	 * @param Request $request
	 *
	 * @return RedirectResponse
	 */
	public function handle(Request $request)
	{
		//dump($request->all());

		admin_success('Processed successfully.');

		return back();
	}

	/**
	 * Build a form here.
	 */
	public function form()
	{
		$this->text('text', 'Text');
		$this->password('password', 'Password')->attribute([
			'placeholder' => '输入密码(Ph)',
		]);
		$this->checkbox('checkbox', 'Checkbox')->options([
			'a' => 'Name',
		])->rules('required')->canCheckAll();
		$this->radio('radio')->options([
			'a' => 'Name',
		])->rules('required');
		$this->email('email')->rules('email');
		$this->captcha('captcha');
		$this->color('color');
		$this->datetime('created_at');
	}

	/**
	 * The data of the form.
	 *
	 * @return array $data
	 */
	public function data()
	{
		return [
			'text'          => '文本输入',
			'text_required' => '文本输入(必填)',
			'name'          => 'John Doe',
			'email'         => 'John.Doe@gmail.com',
			'created_at'    => now(),
		];
	}
}