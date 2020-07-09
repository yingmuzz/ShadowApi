<?php
/**
 * 后台接口路由配置。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

//登录
$router->post('/login', 'Shadow\LoginController@onPost');

//退出登录
$router->post('/logout', 'Shadow\LogoutController@onPost');
