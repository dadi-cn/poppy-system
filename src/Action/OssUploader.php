<?php namespace Poppy\System\Action;

use Exception;
use OSS\OssClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Poppy\System\Classes\Uploader;
use zgldh\QiniuStorage\QiniuStorage;

/**
 * 图片上传
 */
class OssUploader extends Uploader
{
	/**
	 * @var bool 是否在保存后删除本地文件
	 */
	private $deleteLocal = true;

	/**
	 * @var bool 保存到aliyun
	 */
	private $saveAliyun = false;

	/**
	 * @var string 保存类型
	 */
	private $saveType = '';

	/**
	 * OssUploader constructor.
	 * @param string $folder 目录
	 */
	public function __construct(string $folder = 'uploads')
	{
		parent::__construct($folder);
		$save_type = sys_setting('system::oss.save_type');
		if ($save_type === 'aliyun') {
			$this->saveAliyun = true;
		}
		if ($save_type === 'qiniu') {
			$this->saveType = 'qiniu';
		}
	}

	/**
	 * 保存文件输入
	 * @param UploadedFile $file 文件
	 * @return bool|mixed
	 */
	public function saveFile($file)
	{
		if (!parent::saveFile($file)) {
			return false;
		}
		if ($this->saveAliyun) {
			return $this->saveAli($this->deleteLocal);
		}
		if ($this->saveType === 'qiniu') {
			return $this->saveQiniu($this->deleteLocal);
		}

		return true;
	}

	/**
	 * 保存流输入
	 * @param string $content 文件内容
	 * @return bool
	 */
	public function saveInput($content): bool
	{
		if (!parent::saveInput($content)) {
			return false;
		}
		if ($this->saveAliyun) {
			return $this->saveAli($this->deleteLocal);
		}

		if ($this->saveType === 'qiniu') {
			return $this->saveQiniu($this->deleteLocal);
		}

		return true;
	}

	/**
	 * 保存到阿里云
	 * @param bool $delete_local 是否删除本地文件
	 * @return bool
	 */
	private function saveAli($delete_local = true)
	{
		// 设置返回地址
		$returnUrl = sys_setting('system::oss_aliyun.url_prefix');
		$this->setReturnUrl($returnUrl);

		if (!$returnUrl) {
			return $this->setError(trans('system::action.oss_uploader.return_url_error'));
		}

		$endpoint = sys_setting('system::oss_aliyun.endpoint');
		$bucket   = sys_setting('system::oss_aliyun.bucket');

		$accessKeyId      = sys_setting('system::oss_aliyun.access_key');
		$accessKeySecret  = sys_setting('system::oss_aliyun.access_secret');
		$this->saveAliyun = true;
		try {
			$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
			$ossClient->putObject($bucket, $this->destination, $this->storage()->get($this->destination));
			if ($delete_local) {
				$this->storage()->delete($this->destination);
			}

			return true;
		} catch (Exception $e) {
			return $this->setError($e->getMessage());
		}
	}

	/**
	 * 保存到阿里云
	 * @param bool $delete_local 是否删除本地文件
	 * @return bool
	 */
	private function saveQiniu($delete_local = true)
	{
		config([
			'filesystems.disks.qiniu.driver'     => 'qiniu',
			'filesystems.disks.qiniu.domains'    => [
				'default' => sys_setting('system::oss_qiniu.url_prefix'), //七牛域名
				'https'   => '',//HTTPS域名
				'custom'  => '',
			],
			'filesystems.disks.qiniu.access_key' => sys_setting('system::oss_qiniu.access_key'),
			'filesystems.disks.qiniu.secret_key' => sys_setting('system::oss_qiniu.access_secret'),
			'filesystems.disks.qiniu.bucket'     => sys_setting('system::oss_qiniu.bucket'),
		]);

		$returnUrl = sys_setting('system::oss_qiniu.url_prefix');
		$this->setReturnUrl($returnUrl);

		try {
			$Qiniu = QiniuStorage::disk('qiniu');
			$Qiniu->put($this->getDestination(), $this->storage()->get($this->destination));
			if ($delete_local) {
				$this->storage()->delete($this->destination);
			}
			return true;
		} catch (Exception $e) {
			return $this->setError($e->getMessage());
		}
	}
}