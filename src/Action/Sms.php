<?php namespace Poppy\System\Action;

use Poppy\Framework\Classes\Traits\AppTrait;
use Poppy\Framework\Validation\Rule;
use Poppy\System\Classes\Traits\PamTrait;
use Poppy\System\Models\SysConfig;
use Validator;
use View;

/**
 * 短信模板action
 */
class Sms
{
	use AppTrait, PamTrait;

	public const CACHE_TEMPLATES = 'system::action.sms_template';


	/**
	 * @var mixed 所有的模版
	 */
	private $templates;

	/**
	 * @var array 项目条目
	 */
	private $item;

	public function __construct()
	{
		$this->templates = sys_setting(self::CACHE_TEMPLATES) ?: [];
	}


	/**
	 * 获取所有的模版
	 * @return mixed
	 */
	public function getTemplates()
	{
		return $this->templates;
	}

	/**
	 * @return array
	 */
	public function getItem(): array
	{
		return $this->item;
	}

	/**
	 * 新增和编辑
	 * @param array    $data data <br />
	 *                       type     类型 <br />
	 *                       code     代码 <br />
	 *                       content  内容
	 * @param null|int $id
	 * @return bool
	 */
	public function establish($data, $id = null): bool
	{
		if (!$this->checkPam()) {
			return false;
		}

		$type    = sys_get($data, 'type');
		$code    = sys_get($data, 'code');
		$content = sys_get($data, 'content');
		$initDb  = compact('type', 'code', 'content');

		$validator = Validator::make($initDb, [
			'type'    => [
				Rule::required(),
				Rule::in(array_keys(SysConfig::kvSmsType())),
			],
			'code'    => [
				Rule::required(),
			],
			'content' => [
				Rule::required(),
			],
		], [], [
			'type'    => '类型',
			'code'    => '模版代码',
			'content' => '短信内容',
		]);

		if ($validator->fails()) {
			return $this->setError($validator->messages());
		}

		$templates  = $this->templates;
		$Collection = collect($templates);

		if ((clone $Collection)->where('id', '!=', $id)->where('type', $type)->first()) {
			return $this->setError('模板类型已经存在');
		}

		// 修改数据
		if ($id) {
			$index_key    = $this->indexKey($id);
			$initDb['id'] = $id;

			$templates[$index_key] = $initDb;
		}
		else {
			$templates[] = $initDb;
		}

		return $this->save($templates);
	}

	/**
	 * 初始化
	 * @param int $id ID
	 * @return bool
	 */
	public function init($id): bool
	{
		$item = collect($this->templates)->where('id', $id)->first();
		if ($item) {
			$this->item = $item;
			return true;
		}
		return $this->setError(trans('system::action.sms.id_not_exists'));
	}


	/**
	 * 分享
	 */
	public function share(): void
	{
		View::share([
			'item' => $this->item,
		]);
	}

	/**
	 * 刪除
	 * @param int $id id
	 * @return bool
	 */
	public function destroy($id): bool
	{
		$index_key = $this->indexKey($id);
		if (isset($this->templates[$index_key])) {
			unset($this->templates[$index_key]);
		}

		return $this->save($this->templates);
	}

	/**
	 * 发送系统短信
	 * @param string $type   短信类型
	 * @param string $mobile 手机号
	 * @param array  $params 参数
	 * @return mixed
	 */
	public function send($type, $mobile, $params = []): bool
	{
		$sendType = sys_setting('system::sms.send_type') ?: 'local';
		$class    = 'System\\Classes\\Sms\\' . studly_case($sendType) . 'Sms';
		if (!$class) {
			return $this->setError('发送类型不正确');
		}
		/** @var \System\Classes\Contracts\Sms $Sms */
		$Sms = new $class();
		if (!$Sms->send($type, $mobile, $params)) {
			return $this->setError($Sms->getError());
		}

		return true;
	}

	/**
	 * 保存模板
	 * @param array $templates 模板信息
	 * @return bool
	 */
	private function save($templates): bool
	{
		$templates = collect($templates)->map(function ($item, $index) {
			$item['id'] = ++$index;
			return $item;
		})->values()->toArray();

		app('setting')->set(self::CACHE_TEMPLATES, $templates);
		sys_cache('sms')->clear();

		return true;
	}

	/**
	 * 索引
	 * @param int $id id
	 * @return mixed
	 */
	private function indexKey($id)
	{
		return --$id;
	}


}