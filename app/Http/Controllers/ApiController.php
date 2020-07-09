<?php
declare(strict_types=1);
/**
 * Api公用控制器。
 *
 * @author    YingMuzZ <huadyingmu@gmail.com>
 * @copyright © 2020 YingMuzZ
 * @version   v1.0
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

abstract class ApiController extends BaseController
{
    public $user;

    abstract public function onPost();
}
