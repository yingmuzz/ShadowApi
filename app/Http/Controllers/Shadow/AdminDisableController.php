<?php
declare(strict_types=1);
/**
 * 禁用管理员控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers\Shadow;

use App\Model\Shadow\AdminUser;

class AdminDisableController extends ShadowAuthController
{
    public function onPost()
    {
        $i_user_id = $this->post_params['user_id'] ?? 0;
        $o_user = AdminUser::where('status', 1)
            ->where('id', $i_user_id)
            ->first();
        if (!$o_user || $this->user->id == $o_user->id) {
            return $this->errorJson('User.info.error');
        }
        $o_user->status = 0;
        $o_user->save();

        return $this->successJson();
    }
}
