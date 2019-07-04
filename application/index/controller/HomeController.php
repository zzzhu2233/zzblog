<?php

namespace app\index\controller;

use app\index\model\Blog;
use app\index\model\RoleUser;
use app\index\model\User;
use app\index\validate\RegistValidate;
use think\Controller;
use think\Request;
use think\Session;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/7
 * Time: 15:20
 */
class HomeController extends Controller
{
    public function indexAction()
    {
        $id=Session::get('user.id');
        if($id){
            $this -> assign('key',1);
        }
        else{
            $this -> assign('key',0);
        }
        //查询博客信息
        $res = Blog::with('category,user')
            ->order('create_time desc')
            ->paginate(15);
        //dump($res->toArray());
//return;

        $this->assign('data_list', $res);
        return $this->fetch();
    }

    public function detailAction()
    {
        $id = input('id');
        $res = Blog::with('user')
            ->where('id', $id)
            ->find();

        //查询上一篇
        $pre = Blog::order('id desc')
            ->where('id', '<', $id)
            ->limit(1)
            ->find();

        //查询下一篇
        $next = Blog::order('id')
            ->where('id', '>', $id)
            ->limit(1)
            ->find();
        $this->assign('pre', $pre);
        $this->assign('next', $next);
        $this->assign('data', $res);
        return $this->fetch();
    }

    //注册
    public function registAction()
    {
        $validate = new RegistValidate();
        $model = new User();
        $request = Request::instance();
        if ($request->method() == 'GET') {
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            $data = $request->post();
            if ($validate->batch(true)->check($data)) {
                $result = $model->allowField(true)->save($data);
                if ($result) {
                    //为新用户分配 博客用户 角色
                    RoleUser::create([
                        'user_id'=>$model->id,
                        'role_id'=>3
                    ]);
                    $this->redirect('admin/user/login');
                } else {
                    //应该回传错误提示信息
                    $this->redirect('regist');
                }
            }
            else{
                $this->redirect('/index/home/regist', [], 302, [
                    'message' => $validate->getError()
                ]);
            }
        }
    }

}