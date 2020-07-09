<?php
declare(strict_types=1);
/**
 * Redis工具类。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Components;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisTool
{
    /**
     * 数据不锁定。
     */
    const DATA_UNLOCK = 0;

    /**
     * 数据锁定
     */
    const DATA_LOCK = 1;

    /**
     * 单key删除
     */
    const CLEAN_SINGLE = 0;

    /**
     * 批量删除
     */
    const CLEAN_LOT_SIZE = 1;

    /**
     * redis 失效时间。
     */
    const RDS_LIFE_TIME = 600;

    /**
     * 接口休眠时间（微秒）。
     */
    const LOCK_WAIT_TIME = 100000;

    /**
     * redis 锁接口等待时间（秒）。
     */
    const LOCK_TIME = 3;

    /**
     * 获取 redis 数据。
     * @param  string  $key     键值
     * @param  bool    $is_lock 是否加锁
     * @param  string  $token   锁机制值
     * @return mixed
     */
    public static function getRedisData(string $key = '', int $is_lock = 0): bool
    {
        //键存在，直接返回
        if (static::DATA_UNLOCK == $is_lock && !Redis::exists($key)) {
            //键不存在，且不加锁。直接返回false，查库。
            return false;
        }
        $s_value = uniqid();
        //加锁
        do {
            $s_lock_key = 'lock:' . $key;
            if (Redis::exists($key)) {
                if (Redis::exists($s_lock_key) && Redis::get($s_lock_key) == $s_value) {
                    Redis::del($s_lock_key);
                }
                $result = Redis::get($key);
                if ('' == $result || false == $result || is_null($result)) {
                    return false;
                }

                return $result;
            }
            //当键不存在时设置值，返回 true 或 false
            $b_key_locked = Redis::set($s_lock_key, $s_value, 'ex', static::LOCK_TIME, 'nx'); //ex 秒
            if (!$b_key_locked) {
                // 1秒 = 1000000 微秒
                //睡眠，降低抢锁频率，缓解redis压力
                usleep(static::LOCK_WAIT_TIME);
                continue;
            }

            return false;
        } while (!$b_key_locked);
    }

    /**
     * 设置redis。
     * @param string    $key       键名
     * @param mixed     $value     值
     * @param int       $life_time 过期时间
     */
    public static function setRedisData(string $key = '', $value = '', int $life_time = 0): bool
    {
        static::cleanRedisData($key);
        if (is_null($value) || '' === $value) {
            return false;
        } elseif (is_array($value) && !count($value)) {
            return false;
        }
        Redis::set($key, is_array($value) ? json_encode($value) : $value);
        Redis::expire($key, $life_time);

        return true;
    }

     /**
     * 删除 redis 指定的 key。
     * @param  string $key 键名
     * @return bool
     */
    public static function cleanRedisData(string $key = '', int $flag = 0): bool
    {
        try {
            //批量删除
            if (static::CLEAN_SINGLE === $flag) {
                $a_keys = Redis::keys($key . '*');
                if (!count($a_keys)) {
                    return false;
                }
                Redis::del($a_keys);

                return true;
            }
            //普通删除
            if (Redis::exists($key)) {
                Redis::del($key);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('cleanRedisDataErroor:' . $key . 'msg:' . $e->getMessage());

            return false;
        }
    }
}
