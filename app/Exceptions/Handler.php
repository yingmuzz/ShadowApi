<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $i_code = 500;
        $arr = array(
            'code' => $i_code,
            'msg' => 'The server encountered an unknown exception～',
            'data' => ''
        );
        if ($e instanceof NotFoundHttpException) {
            $arr['msg'] = 'Server is down～';
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $arr['msg'] = 'Server is off～';
        } else {
            $arr['code'] = $e->getCode();
            $arr['msg'] = $e->getMessage();
            $i_code = 0;
        }

        return response()->json($arr, 200);
    }
}
