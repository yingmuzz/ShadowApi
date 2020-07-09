<?php
declare(strict_types=1);
/**
 * 登录控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers\Shadow;

use App\Model\Shadow\AdminUser;
use App\Http\Components\GoogleAuthenticator;

class LoginController extends ShadowAuthController
{
    /**
     * 防止爆破redis key。
     */
    const ATTACK_RDS_LOGIN_KEY = 'attack_shadow_login:';

    /**
     * 是否强制登录。
     */
    const MUST_USER_LOGIN = true;

    public function onPost()
    {
        $s_ip_key = static::ATTACK_RDS_LOGIN_KEY . $_SERVER['REMOTE_ADDR'] ?? '';
        $s_account = $this->post_params['account'] ?? '';
        $s_password = $this->post_params['password'] ?? '';
        $s_code = $this->post_params['code'] ?? '';
        if (AdminUser::ACCOUNT_MIN_LINGTH > strlen($s_account) || AdminUser::PASSWORD_MIN_LINGTH > strlen($s_password) || strlen($s_code) != AdminUser::GOOGLE_CODE_LENGTH) {
            $this->writeVisitRecords($s_ip_key);

            return $this->errorJson('Login.input.error');
        }
        //根据账号查询是否存在记录
        $o_user = AdminUser::where('status', AdminUser::STATUS_ENABLE)
            ->where('account', $s_account)
            ->first();
        if (!$o_user) {
            $this->writeVisitRecords($s_ip_key);

            return $this->errorJson('Login.user.not.found');
        }
        //验证Google验证码
        $o_google_auth = new GoogleAuthenticator;
        $b_code = $o_google_auth->verifyCode($o_user->google_auth, $s_code);
        if (true !== $b_code) {
            $this->writeVisitRecords($s_ip_key);

            return $this->errorJson('Login.google.code.error');
        }
        //校验密码
        $o_user->checkPassword($s_password);
        //登录成功
        $a_result = array(
            'uuid' => md5($o_user->id . env('APP_KEY')),
            'token' => $o_user->generateToken()
        );

        return $this->successJson($a_result);
    }
}
