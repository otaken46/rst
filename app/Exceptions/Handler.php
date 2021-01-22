<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($this->isHttpException($exception)) {
            if($exception->getStatusCode() == 404 || $exception->getStatusCode() == 405){
                //405はpostかget形式で定義していない場合になるAPIのURL直接ブラウザ表示対応
                Log::debug("render1111");
                return redirect('/');
            }
        }else{
            if($exception instanceof \Illuminate\Session\TokenMismatchException){
                Log::debug("render2222");
                return redirect('/');
            }
        }
        return parent::render($request, $exception);
    }
}
