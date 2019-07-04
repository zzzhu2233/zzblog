<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/11
 * Time: 20:15
 */

namespace app\index\validate;
use think\Validate;
class RegistValidate extends Validate
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