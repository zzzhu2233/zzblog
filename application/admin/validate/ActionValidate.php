<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 14:41
 */

namespace app\admin\validate;

use think\Validate;

class ActionValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:10',
        'rule' => 'require'
    ];
    protected $field = [
        'title' => '权限名称',
        'rule' => '权限规则'
    ];
}