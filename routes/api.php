<?php
/**
 * Api接口路由分发配置。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

$router->get('/', function () use ($router) {
    echo 'ok';
});

//后台路由组
$router->group(["prefix" => "shadow", 'middleware' => 'Shadow'], function () use ($router) {
    require_once __DIR__ . '/../routes/shadow.php';
});
