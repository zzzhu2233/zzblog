<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 10:57
 */

namespace app\extra;

use app\admin\model\Action;
use app\admin\model\RoleAction;
use app\admin\model\RoleUser;
use think\Request;
use think\Session;

class checkAuth
{
    public function run()
    {
        //如果用户请求的是user控制器下的login 方法，则不进行登录验证
        $request = Request::instance();

        /*$controller = $request->controller();
        $action = $request->action();
        if (strtolower($controller) == 'user' && strtolower($action) == 'login') {
            return;
        }*/
        if ($request->path() == 'admin/user/login') {
            return;
        }
        //如果是注销操作则不进行权限验证
        if($request->path() == 'admin/user/logout'){
            return;
        }

        //验证用户是否登录，如果登录，则继续用户的请求，如果没有登录，则跳转到登录页面
        if (Session::get('user')) {
            //权限验证
            $result = checkPri::checkAction(strtolower($request->path()));
            if (!$result) {
                redirect('/admin/user/login', [], 302, ['message' => '没有权限'])->send();
                die();
            }
        } else {
            redirect('/admin/user/login', [], 302, [
                'message' => '没有登录'
            ])->send();
            die();
        }
    }
}