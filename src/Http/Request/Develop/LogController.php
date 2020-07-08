<?php namespace Poppy\System\Http\Request\Develop;

use File;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Request;
use Redirect;
use Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Poppy\System\Classes\LogViewer;

/**
 * 显示日志
 */
class LogController extends DevelopController
{
	/**
	 * 入口
	 * @return Factory|RedirectResponse|View|BinaryFileResponse
	 */
	public function index()
	{
		if (input('l')) {
			LogViewer::setFile(base64_decode(input('l')));
		}

		if (input('dl')) {
			return Response::download(storage_path() . '/logs/' . base64_decode(input('dl')));
		}

		if (Request::has('del')) {
			File::delete(storage_path() . '/logs/' . base64_decode(input('del')));

			return Redirect::to(Request::url());
		}

		$logs = LogViewer::all();

		return view('system::develop.log.index', [
			'logs'         => $logs,
			'files'        => LogViewer::getFiles(true),
			'current_file' => LogViewer::getFileName(),
		]);
	}
}