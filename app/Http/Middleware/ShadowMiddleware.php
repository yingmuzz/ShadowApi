<?php
declare(strict_types=1);
/**
 * Api接口中间件。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;

class ShadowMiddleware
{
    /**
     * 处理请求。
     * @param  \Illuminate\Http\Request $request 请求
     * @param  \Closure $next    向下执行指针
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::debug("---------------------Start--------------------");
        $i_start_time = microtime(true);
        $s_url = $_SERVER['REQUEST_URI'] ?? '';
        Log::debug('Request_url:' . $s_url);
        Log::debug('Remote_addr:' . $_SERVER['REMOTE_ADDR']);
        $s_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        Log::debug("Http_x_forwarded_for:" . $s_x_forwarded_for);
        Log::debug("Request:" . json_encode($_POST));
        $response = $next($request);
        $i_end_time = microtime(true);
        $i_take_time = $i_end_time - $i_start_time;
        $i_take_time = round($i_take_time, 3);
        Log::debug("Response:" . json_encode($response));
        Log::debug("耗时:" . $i_take_time . "秒");
        Log::debug("-------------------------------------------");

        return $response;
    }
}
