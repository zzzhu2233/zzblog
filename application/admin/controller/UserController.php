<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1
 * Time: 9:10
 */

namespace app\admin\controller;

use app\admin\model\Role;
use app\admin\model\RoleUser;
use app\admin\model\User;
use app\admin\validate\UserValidate;
use think\Controller;
use think\Request;
use think\Session;

class UserController extends Controller
{

    public function indexAction()
    {

        //$res=User::find(2);
        //使用预载入功能
        $res = User::with('userInfo')->find(2);
        //查询对应的关联模型中的电话
        //echo ($res->userInfo->tell);

        //通过用户模型查询用户详细信息以及所发表的博客信息
        /*$res = User::with('userInfo,Blog')
            ->order('id desc')
            ->select();
        foreach($res as $item){
            dump($item->toArray());
        }
        return;*/


        $res = User::with('userInfo')
            ->order('id desc')
            ->select();
        $this->assign('user_list', $res);
        $this->assign('active', 'user_index');
        return $this->fetch();
    }

    public function setAction()
    {
        $this->assign('active', 'user_list');
        $id = input('id');
        $this->assign('id', $id);
        $roleUser = new \app\admin\model\RoleUser();
        //首先要区分是get还是post请求
        $request = Request::instance();
        if ($request->method() == 'GET') {
            $message = Session::get('message') ?: [];
            $this->assign('message', $message);
            $data = Session::get('data') ?: [];
            if (count($data) == 0) {
                if (!is_null($id)) {
                    $data = User::get($id);
                }
            }

            $this->assign('data', $data);
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            dump(3);
            //获取表单提交的值

            $data = $request->post();
            //验证
            $validate = new UserValidate();
            dump($id);
            //首先执行验证，如果验证通过则执行添加或者更新操作，如果验证失败
            if ($validate->batch(true)->check($data)) {
                //如果实例化空的Blog类的对象，则调用 save 方式时，执行的是insert 操作，如果Blog类对象不是空的，则执行更新操作
                $model = null;
                if (!is_null($id)) {
                    $model = User::get($id);
                } else {
                    $model = new User();
                }
                $res = $model->allowField(true)
                    ->data($data)
                    ->save();
                $roleUser->data([
                    'role_id' => 3,
                    'user_id' => $model->id
                ]);
                $roleUser->save();


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

    //修改密码
    public function setPasswordAction()
    {
        $request = Request::instance();
        if($request->method() == 'GET')
        {
        }
        if($request->method() == 'POST')
        {
            $old = input("old");
            $newpwd = input("newpwd");
            $id = Session::get('user.id');
            $user = new \app\admin\model\User();
            $data = $user->field('pwd')->find($id)->toArray();
            $pwd = $data['pwd'];
            if(md5($old)!=$pwd)
            {
//                return alert_error('原密码错误');
//                return "原密码错误";
//                $this->redirect('setPassword',2);
                $this->success('原密码错误','setPassword','',3);
            }
            elseif (md5($old)==$pwd)
            {
                $user->save([
                    'pwd' => $newpwd
                ],['id' => $id]);
                $this->redirect('index');
            }

        }
        $this->assign('active', 'setpassword');
        return $this->fetch();
    }

    //完善个人资料
    public function setInfoAction()
    {
        $name=input('names');
        $address=input('address');
        $tlephone=input('tlephone');
        $request = Request::instance();
        $user = new \app\admin\model\User();
        $user_info = new \app\admin\model\UserInfo();
        if($request->method() == 'GET')
        {
        }
        if($request->method() == 'POST')
        {
            $file = request()->file('image');
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'static' . DS . 'images' . DS . 'assets');
                if($info){
                    // 成功上传后 获取上传信息
                    // 输出 jpg
                    // dump($info->getExtension()) ;
                    // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                    // dump($info->getSaveName()) ;
                    // 输出 42a79759f284b767dfcb2a0197904287.jpg
                    // dump($info->getFilename()) ;
                     $pathTemp = $info->getSaveName();
                     $pathTemp = str_replace('\\','/',$pathTemp);
                     $filePath = 'images/assets/'.$pathTemp;
                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
            $id = Session::get('user.id');

            $nowTime = time();
            if($user_info->where("user_id",$id)->find())
            {
                $user_info->save([
                    'realname' => $name,
                    'address' => $address,
                    'tell' => $tlephone,
                    'create_time' => $nowTime,
                    'update_time' => $nowTime
                ],['user_id' => $id]);
                $user->where("id",$id)->Update(['img' => $filePath]);
            }
            else{
                $user_info->data([
                    'realname' => $name,
                    'address' => $address,
                    'tell' => $tlephone,
                    'user_id' => $id,
                    'create_time' => $nowTime,
                    'update_time' => $nowTime
                    ]);
                $user_info->save();
                $user->save([
                    'img' => $filePath
                ],['id' => $id]);
                $user->where("id",$id)->Update(['img' => $filePath]);
            }
        }

        $this->assign('active', 'setinfo');
        return $this->fetch();
    }

    //小黑屋
    public function blackHouseAction()
    {
        $roleUser = new \app\admin\model\RoleUser();
        $res = User::with('userInfo')->find(2);
        $res = User::with('userInfo')
            ->order('id desc')
            ->select();
        $id = input('id');
        $key = input('key');
        if($id)
        {
            if($key == 0)
            {
                // $data = $roleUser->where('user_id',$id)->find()->toArray();
                // dump($data);
                $roleUser->where('user_id',$id)->Update(['role_id' => 0]);
                $this->success('拉黑成功','blackHouse','',3);
            }
            if($key == 1)
            {
                $roleUser->where('user_id',$id)->Update(['role_id' => 3]);
                $this->success('取消成功','blackHouse','',3);
            }
        }


        $this->assign('user_list', $res);
        $this->assign('active', 'black_house');
        return $this->fetch();
    }

    public function loginAction()
    {
        $request = Request::instance();
        if ($request->method() == 'GET') {
            $message = Session::get('message') ?: '';
            $this->assign('message', $message);
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            //验证登录信息是否正确
            //获取用户填写的用户名和密码
            $username = input('username');
            $password = input('password');
            //find方法如果查询到数据，则返回user类型的对象，否则返回null
            $res = User::where([
                'username' => $username,
                'pwd' => md5($password)
            ])->find();
            /*if后面的小括号中的值会自动转换为true或者false:转换原则如下:
            值如果为非0的数字，非空的字符串或者非null 的对象，都会转换为 true，
            值如果为数字0，空字符串("")，或者null，都会转换为false
            */
            if ($res) {
                //先将用户信息存储到session中
                Session::set('user', $res);
                $this->redirect('/admin/user/setInfo');
            } else {
                $this->redirect('login', [], 302, [
                    'message' => '用户名或密码错误'
                ]);
            }
        }
    }

    //为用户赋予角色
    public function setRoleAction()
    {
        $id=input('id');
        $this->assign('id',$id);
        $this->assign('active', 'user_index');
        $request = Request::instance();
        if ($request->method() == 'GET') {
            //根据id查询用户信息
            $user = User::get($id);
            $this->assign('username', $user->username);
            //查询所有角色
            $this->assign('data_list', Role::all());

            //查询用户已经的角色
            $roles=RoleUser::where('user_id',$id)->column('role_id');
            $this->assign('hasRoles',$roles);

            return $this->fetch();
            //展示所有角色
        } else if($request->method()=='POST') {
            $user_id = $id;
            //获取用户选择的角色id
            $roles_id = input('roles/a', []);
//组装数据
            if (count($roles_id) > 0) {
                $data = [];
                foreach ($roles_id as $role_id) {
                    $data[] = [
                        'role_id' => $role_id,
                        'user_id' => $user_id
                    ];
                }
//                dump($data);
                $model = new RoleUser();
                //先删除用户已有角色，再添加新的角色
                $model->where('user_id',$id)->delete();
                $res = $model->saveAll($data);
                if ($res){
                    $this->redirect('index');
                }
            } else {
                //提示错误信息
            }
        }
    }
    //注销
    public function logoutAction(){
        Session::delete('user');
        $this->redirect('login');
    }
    //用户删除
    public function delAction(){
        User::destroy(input('id'));
        $this->redirect('index');
    }
}