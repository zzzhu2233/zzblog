<?php

namespace app\admin\controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31
 * Time: 9:06
 */
use app\admin\validate\BlogValidate;
use think\Controller;
use app\admin\model\Blog;
use app\admin\model\Category;
use think\Request;
use think\Session;
use think\Validate;

class BlogController extends Controller
{
    public function indexAction()
    {
//获取搜索关键词
        $key = input('key');
        /*初始写法
        $res = null;
        if (empty($key)) {
            $res = Blog::with('user,category')
                ->order('create_time desc')
                ->paginate(10);
        } else {
            $res = Blog::with('user,category')
                ->where('title', 'like', '%' . $key . '%')
                ->whereOr('content', 'like', '%' . $key . '%')
                ->order('create_time desc')
                ->paginate(10);
        }*/
        /*第二版写法*/
        $res = Blog::with('user,category')
            ->order('create_time desc');
        if (!empty($key)) {
            $res = $res->where('title', 'like', '%' . $key . '%')
                ->whereOr('content', 'like', '%' . $key . '%');
        }
        $res = $res->paginate(10);


        $this->assign('blog_list', $res);
        $this->assign('active', 'blog_index');
        return $this->fetch();
    }

    public function setAction()
    {

        $this->assign('active', 'blog');
        $id = input('id');
        $this->assign('id', $id);
        //首先要区分是get还是post请求
        $request = Request::instance();
        if ($request->method() == 'GET') {
            //获取错误信息，如果有，则将错误信息通过变量分配到视图
            /*如果session中有 message 键(说明是验证错误后跳转过来的)，则从session 中获取值并复制给变量message
            ,如果,session 中没有 message 键，则将变量 message 赋值为 [](空数组)*/
            $message = Session::get('message') ?: [];
            $this->assign('message', $message);
            $data = Session::get('data') ?: [];
            /*如果数组为空，说明不是发生错误跳转过来的，那么久判断id是否为空，如果非空，久根据id查询数据，重新给 data 赋值
            如果id 为空,则什么都不做；如果数组不为空，说明是发生错误跳转过来的，则久不再为 data 重新赋值
            */
            if (count($data) == 0) {
                if (!is_null($id)) {
                    $data = Blog::get($id);
                }
            }

            $this->assign('data', $data);
            $this->assign('category_list', Category::all());
            return $this->fetch();
        } else if ($request->method() == 'POST') {
            //获取表单提交的值
            $data = $request->post();
            //验证
            $validate = new BlogValidate();
            //首先执行验证，如果验证通过则执行添加或者更新操作，如果验证失败
            if ($validate->batch(true)->check($data)) {
                //如果实例化空的Blog类的对象，则调用 save 方式时，执行的是insert 操作，如果Blog类对象不是空的，则执行更新操作
                $blogModel = null;
                if (!is_null($id)) {
                    $blogModel = Blog::get($id);
                } else {
                    $blogModel = new Blog();
                }
                $data['user_id']=Session::get('user.id');
                $res = $blogModel->allowField(true)
                    ->data($data)
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

    public function detailAction()
    {

        $res = Blog::get(30);
        $this->assign('data', $res);
        return $this->fetch();
    }


    public function delAction()
    {

        $id = input('id');
        $res = Blog::destroy($id, true);
        if ($res > 0) {
            $this->redirect('index');
        } else {
            return '删除失败';
        }

    }

    //处理上传的图片
    public function uploadAction()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('ajaxFile');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            //DS：常量，会根据当前的系统动态决定路径分隔符，在windows 系统下会变成"\"，在linux 下会变成 "/"
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                return config('domain') . 'uploads' . DS . $info->getSaveName();
            } else {
                // 上传失败获取错误信息
                return $file->getError();
            }
        }
    }

    //隐藏的博客
    public function hideBlogAction()
    {

        $this->assign('active', 'hide_blog');
        return $this->fetch();
    }

    //我的博客列表
    public function myBlogAction()
    {
        $res = Blog::with('user,category')
            ->where('user_id',Session::get('user.id'))
            ->order('create_time desc')
            ->paginate(10);
        $this->assign('blog_list', $res);
        $this->assign('active', 'my_blog');
        return $this->fetch();
    }
}