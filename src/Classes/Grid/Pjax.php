<?php

namespace Poppy\System\Classes\Grid;


use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Poppy\System\Models\PamAccount;
use Response;

class Pjax
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$request->pjax() || $response->isRedirection() || app('auth')->guard(PamAccount::TYPE_BACKEND)->guest()) {
            return $response;
        }

        if (!$response->isSuccessful()) {
            return $this->handleErrorResponse($response);
        }

        try {
            response()->header('X-PJAX-URL', $request->getRequestUri());
        } catch (Exception $exception) {
        }

        return $response;
    }

    /**
     * Send a response through this middleware.
     *
     * @param Response|mixed $response
     */
    public static function respond($response)
    {
        $next = function () use ($response) {
            return $response;
        };

        (new static())->handle(Request::capture(), $next)->send();

        exit;
    }

    /**
     * Handle Response with exceptions.
     *
     * @param Response $response
     * @return RedirectResponse
     */
    protected function handleErrorResponse(Response $response)
    {
        $exception = $response->exception;

        $error = new MessageBag([
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        return back()->withInput()->withErrors($error, 'exception');
    }
}
