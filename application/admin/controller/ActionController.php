<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 8:53
 */

namespace app\admin\controller;
use app\admin\validate\ActionValidate;
use think\Controller;
use think\Request;
use think\Session;
use app\admin\model\Action;

class ActionController extends Controller
{
    public function indexAction()
    {
        $res = Action::all();
        $this->assign('data_list', $res);
        $this->assign('active', 'action_index');
        return $this->fetch();
    }
    public function setAction(){

        $this->assign('active', 'action_index');
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
                    $data = Action::get($id);
                }
            }

            $this->assign('data', $data);
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            //获取表单提交的值
            $data = $request->post();
            //验证
            $validate = new ActionValidate();
            //首先执行验证，如果验证通过则执行添加或者更新操作，如果验证失败
            if ($validate->batch(true)->check($data)) {
                //如果实例化空的Blog类的对象，则调用 save 方式时，执行的是insert 操作，如果Blog类对象不是空的，则执行更新操作
                $model = null;
                if (!is_null($id)) {
                    $model = Action::get($id);
                } else {
                    $model = new Action();
                }
                $res = $model->allowField(true)
                    ->data($request->post())
                    ->save();
                if ($res) {
                    $this->redirect('index');
                } else {
                    return '没有更新任何纪录';
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

}