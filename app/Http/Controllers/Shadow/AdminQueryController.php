<?php
declare(strict_types=1);
/**
 * 管理员列表控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers\Shadow;

use App\Model\Shadow\AdminUser;

class AdminQueryController extends ShadowAuthController
{
    public function onPost()
    {
        $i_page = $this->post_params['current'] ?? 1;
        $i_page_size = $this->post_params['size'] ?? static::PAGE_SIZE;
        $i_status = $this->post_params['status'] ?? -1;
        $s_status_eq = '=';
        if (-1 == $i_status) {
            $s_status_eq = '!=';
        }
        $i_count = AdminUser::where('status', $s_status_eq, $i_status)->count();
        $o_users = AdminUser::where('status', $s_status_eq, $i_status)
            ->orderBy('id', 'DESC')
            ->skip(($i_page - 1) * $i_page_size)
            ->take($i_page_size)
            ->get();
        $a_rows = array();
        if (count($o_users)) {
            foreach ($o_users as $user) {
                $a_rows[] = array(
                    'user_id' => $user->id,
                    'account' => $user->account,
                    'status' => $user->status,
                    'add_time' => $user->add_time,
                    'permission_btn_edit' => $user->status == 1 ? true : false,
                    'permission_btn_del' => $user->status == 1 ? true : false,
                    'permission_btn_google' => $user->status == 1 ? true : false
                );
            }
        }
        $a_result = array(
            'pagination' => array(
                'current' => $i_page,
                'size' => $i_page_size,
                'total' => $i_count,
            ),
            'list' => $a_rows
        );

        return $this->successJson($a_result);
    }
}
