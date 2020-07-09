<?php
declare(strict_types=1);
/**
 * Api异常处理。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    /**
     * 错误定义集合.
     * @var array
     */
    protected static $errors = [
        'Token.error'                                => [-1,       '身份令牌无效～'],
        'Api.wait'                                   => [-100,     '接口请求频繁～'],
        'Password.generate.error'                    => [10000,    '请输入12~40位密码～'],
        'Login.input.error'                          => [10001,    '账号或密码输入错误～'],
        'Login.user.not.found'                       => [10002,    '账号或密码输入错误～'],
        'Login.google.code.error'                    => [10003,    '账号或密码输入错误～'],
        'Password.check.error'                       => [10004,    '账号或密码输入错误～'],
    ];

    /**
     * 错误提示结果。
     * @param  string $package 所在包
     * @return mixed
     */
    public static function errorJson(string $package)
    {
        $i_code = static::$errors[$package][0] ?? 0;
        $s_message = static::$errors[$package][1] ?? 'Unknow error';
        $arr = array(
            'code' => $i_code,
            'msg' => $s_message,
            'data' => ''
        );

        return response()->json($arr, 200);
    }

    /**
     * 异常返回结果。
     * @param  string $package 所在类
     * @param  string $desc    描述
     * @return mixed
     */
    public static function exJson(string $package = '', string $desc = '')
    {
        $i_code = static::$errors[$package][0] ?? 0;
        $s_message = static::$errors[$package][1] ?? $desc;

        throw new \Exception($s_message, $i_code);
    }
}
