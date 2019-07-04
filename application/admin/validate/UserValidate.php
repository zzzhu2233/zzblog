<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/7
 * Time: 14:25
 */

namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'username' => 'require|max:20|unique:user',
        'pwd' => 'require|length:6,18',
        'confirm_pwd' => 'confirm:pwd'
    ];
    protected $field = [
        'username' => '用户名',
        'pwd' => '密码'
    ];
    protected $message = [
        'confirm_pwd' => '两次输入的密码不一致'
    ];
}