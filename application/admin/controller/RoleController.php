<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 8:53
 */

namespace app\admin\controller;

use app\admin\model\Action;
use app\admin\model\RoleAction;
use app\admin\validate\RoleValidate;
use think\Controller;

use app\admin\model\Role;
use think\Request;
use think\Session;

class RoleController extends Controller
{
    public function indexAction()
    {
        $res = Role::all();
        $this->assign('role_list', $res);
        $this->assign('active', 'role_index');
        return $this->fetch();
    }

    public function setAction()
    {

        $this->assign('active', 'role_index');
        $id = input('id');
        $this->assign('id', $id);
        //首先要区分是get还是post请求
        $request = Request::instance();
        if ($request->method() == 'GET') {
            $message = Session::get('message') ?: [];
            $this->assign('message', $message);
            $data = Session::get('data') ?: [];

            if (count($data) == 0) {
                if (!is_null($id)) {
                    $data = Role::get($id);
                }
            }

            $this->assign('data', $data);
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            //获取表单提交的值
            $data = $request->post();
            //验证
            $validate = new RoleValidate();
            //首先执行验证，如果验证通过则执行添加或者更新操作，如果验证失败
            if ($validate->batch(true)->check($data)) {
                //如果实例化空的Blog类的对象，则调用 save 方式时，执行的是insert 操作，如果Blog类对象不是空的，则执行更新操作
                $model = null;
                if (!is_null($id)) {
                    $model = Role::get($id);
                } else {
                    $model = new Role();
                }
                $res = $model->allowField(true)
                    ->data($request->post())
                    ->save();
                if ($res) {
                    $this->redirect('index');
                } else {
                    return '更新失败';
                }
            } else {
//                dump($validate->getError());
//                return;
                //使用 redirect 跳转页面都是 get 请求
                //通过第四个参数传递数据，其实是将数据存储到 session 中了，只不过这个 session 是一次性的
                $this->redirect('set', [], 302, [
                    'message' => $validate->getError(),
                    'data' => $data
                ]);
            }
        }
    }

    //为角色授权
    public function setActionAction()
    {
        $id = input('id');
        $this->assign('id', $id);
        $this->assign('active', 'role_index');
        $request = Request::instance();
        if ($request->method() == 'GET') {
            //根据id查询角色信息
            $role = Role::get($id);
            $this->assign('rolename', $role->title);
//            //查询所有权限
            $this->assign('data_list', Action::all());

            //查询角色已有的权限
             $actions=RoleAction::where('role_id',$id)->column('action_id');
            $this->assign('hasAction',$actions);

            return $this->fetch();
            //展示所有角色
        } else if ($request->method() == 'POST') {
            //获取用户选择的权限id
            $actions_id = input('actions/a', []);
//组装数据
            if (count($actions_id) > 0) {
                $data = [];
                foreach ($actions_id as $action_id) {
                    $data[] = [
                        'role_id' => $id,
                        'action_id' => $action_id
                    ];
                }
//                dump($data);
                $model = new RoleAction();
                //先删除当前角色已有的权限，然后再添加新的权限
                $model->where('role_id', $id)->delete();
                $res = $model->saveAll($data);
                if ($res) {
                    $this->redirect('index');
                }
            } else {
                //提示错误信息
            }
        }
    }

}