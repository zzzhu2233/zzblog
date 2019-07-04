<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5
 * Time: 14:45
 */

namespace app\admin\validate;

use think\Validate;

class BlogValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:50',
        'content' => 'require'
    ];
//    protected $message=[
//        'title'=>'博客标题不能为空',
//        'content'=>'博客内容不能为空'
//    ];

    protected $field = [
        'title' => '博客标题',
        'content' => '博客内容'
    ];
}