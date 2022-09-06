<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Inertia\Inertia;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Prepare exception for rendering.
     *
     * @param $request
     * @param  \Throwable  $e
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|object|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (!app()->environment(['local', 'testing']) && in_array($response->status(), [500, 404, 403], true)) {
            switch($response->status()) {
                case 403: $message = __('Action is not allowed for the logged in user'); break;
                case 404: $message = __('Page was not found'); break;
                case 500: $message = __('An error has occurred on the server'); break;
                default:  $message = __('Unexpected error has occurred');
            }

            return redirect()->back()->dangerBanner($message);
        }

        return $response;
    }
}
