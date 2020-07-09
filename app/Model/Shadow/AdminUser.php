<?php
declare(strict_types=1);
/**
 * 后台管理员模型。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Model\Shadow;

use JWTAuth;
use App\Exceptions\ApiException;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    /**
     * 启用。
     */
    const STATUS_ENABLE = 1;

    /**
     * 禁用。
     */
    const STATUS_DISABLE = 0;

    /**
     * 账号最小长度。
     */
    const ACCOUNT_MIN_LINGTH = 6;

    /**
     * 密码最小长度。
     */
    const PASSWORD_MIN_LINGTH = 12;

    /**
     * 密码最大长度。
     */
    const PASSWORD_MAX_LINGTH = 40;

    /**
     * 密码生成计算复杂度。
     */
    const PASSWORD_COST = 12;

    /**
     * Google验证码长度。
     */
    const GOOGLE_CODE_LENGTH = 6;

    /**
     * 后台用户信息表。
     *
     * @var string
     */
    protected $table = 'shadow_users';

    /**
     * 是否启用时间戳。
     * @var bool
     */
    public $timestamps = false;

    /**
     * 生成用户身份令牌。
     * @return string
     */
    public function generateToken()
    {
        return JWTAuth::fromUser($this);
    }

    /**
     * 生成密码。
     * @param  string $password 密码
     * @return mixed
     */
    public static function generatePassword(string $password = ''): string
    {
        if ('' == $password || null == $password || static::PASSWORD_MIN_LINGTH > strlen($password) || static::PASSWORD_MAX_LINGTH < strlen($password)) {
            ApiException::exJson('Password.generate.error');
        }

        return password_hash($password, PASSWORD_BCRYPT, array('cost' => static::PASSWORD_COST));
    }

    /**
     * 检查密码是否一致。
     * @param  string $password  密码明文
     * @return bool
     */
    public function checkPassword(string $password = ''): bool
    {
        if ('' == $password || null == $password || static::PASSWORD_MIN_LINGTH > strlen($password) || static::PASSWORD_MAX_LINGTH < strlen($password)) {
            ApiException::exJson('Password.check.error');
        }
        $b_flag = password_verify($password, $this->password);
        if (true !== $b_flag) {
            ApiException::exJson('Password.check.error');
        }

        return true;
    }
}
