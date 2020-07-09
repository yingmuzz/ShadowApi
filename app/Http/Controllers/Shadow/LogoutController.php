<?php
declare(strict_types=1);
/**
 * 登出登录控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers\Shadow;

use JWTAuth;

class LogoutController extends ShadowAuthController
{
    /**
     * 是否强制登录。
     */
    const MUST_USER_LOGIN = false;

    public function onPost()
    {
        try {
            $s_token = $this->post_params['token'] ?? '';
            if ('' != $s_token) {
                JWTAuth::invalidate($s_token);
            }
        } catch (\Exception $e) {
        }

        return $this->successJson();
    }
}
