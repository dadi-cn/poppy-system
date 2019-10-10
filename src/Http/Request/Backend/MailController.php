<?php namespace Poppy\System\Http\Request\Backend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Input;
use Mail;
use Poppy\Framework\Classes\Resp;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Mail\TestMail;
use Throwable;
use Validator;

/**
 * 邮件控制器
 */
class MailController extends BackendController
{
	public function __construct()
	{
		parent::__construct();

		self::$permission = [
			'global' => 'backend:system.global.manage',
		];
	}

	/**
	 * 保存邮件配置
	 * @return JsonResponse|RedirectResponse|Response|Redirector
	 */
	public function store()
	{
		if (is_post()) {
			$Setting = app('setting');
			$all     = Input::all();
			foreach ($all as $key => $value) {
				if (!$value) {
					continue;
				}
				$Setting->set('system::mail.' . $key, $value);
			}

			return Resp::web(Resp::SUCCESS, '更新邮件配置成功', 'reload|1');
		}

		$Setting = app('setting');
		$data    = [
			'driver'     => $Setting->get('system::mail.driver'),
			'encryption' => $Setting->get('system::mail.encryption'),
			'port'       => $Setting->get('system::mail.port'),
			'host'       => $Setting->get('system::mail.host'),
			'from'       => $Setting->get('system::mail.from'),
			'username'   => $Setting->get('system::mail.username'),
			'password'   => $Setting->get('system::mail.password'),
		];

		return view('system::backend.mail.establish', [
			'item' => $data,
		]);
	}

	/**
	 * 测试邮件发送
	 */
	public function test()
	{
		if (is_post()) {
			$mail      = input('to');
			$content   = input('content');
			$initDb    = [
				'mail'    => $mail,
				'content' => '这是一封测试邮件, 附加内容' . $content,
			];
			$validator = Validator::make($initDb, [
				'mail'    => [
					Rule::email(),
					Rule::required(),
				],
				'content' => [
					Rule::required(),
					Rule::string(),
				],
			]);
			if ($validator->fails()) {
				return Resp::web(Resp::ERROR, $validator->messages());
			}
			try {
				Mail::to($mail)->send(new TestMail($content));

				return Resp::web(Resp::SUCCESS, '邮件发送成功');
			} catch (Throwable $e) {
				return Resp::web(Resp::ERROR, $e->getMessage());
			}
		}

		return view('system::backend.mail.test');
	}
}
