<?php
declare(strict_types=1);
/**
 * Api公用控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers\Shadow;

use Log;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\ApiController;

class ShadowAuthController extends ApiController
{
    /**
     * 单位时间。
     */
    const ATTACK_UNIT_TIME = 60;

    /**
     * 单位时间。
     */
    const ATTACK_MAX_TRY_TIMES = 5;

    /**
     * redis过期时间。
     */
    const ATTACK_RDS_LIFE_TIME = 600;

    /**
     * http响应成功状态码。
     */
    const HTTP_RESPONSE_OK = 200;

    /**
     * 接收POST请求的数据。
     * @var array
     */
    protected $post_params = [];

    public function __construct(Request $request)
    {
        $this->post_params = $request->all();
    }

    /**
     * 授权用户。
     * @var AdminUser
     */
    public $user;

    public function onPost()
    {
    }

    /**
     * 成功响应结果。
     * @param  array  $data 返回结果
     * @return mixed
     */
    protected function successJson(array $data = [])
    {
        $arr = array(
            'code' => 1,
            'msg' => '',
            'data' => $data
        );

        return response()->json($arr, static::HTTP_RESPONSE_OK);
    }

    /**
     * 错误提示json返回。
     * @param  string $package 所在控制器
     * @return mixed
     */
    public function errorJson(string $package = '')
    {
        return ApiException::errorJson($package);
    }

    /**
     * 写入redis记录。
     * @param  string $key       键名
     * @param  int    $life_time 键名
     * @return bool
     */
    public function writeVisitRecords(string $key = '', int $life_time = self::ATTACK_RDS_LIFE_TIME): bool
    {
        Redis::rPush($key, time());
        redis::expire($key, $life_time);
        $this->checkAttackRecords($key);

        return true;
    }

    /**
     * 检查redis记录。
     * @param  string $key 键名
     * @return bool
     */
    public function checkAttackRecords(string $key = ''): bool
    {
        $b_flag = Redis::exists($key);
        if (false === $b_flag) {
            return true;
        }
        $i_length = Redis::lLen($key);
        if ($i_length > static::ATTACK_MAX_TRY_TIMES) {
            $time = Redis::lindex($key, ($i_length - static::ATTACK_MAX_TRY_TIMES - 1));
            if ((time() - $time) < static::ATTACK_UNIT_TIME) {
                ApiException::exJson('Api.wait');
            }
        }

        return true;
    }
}
