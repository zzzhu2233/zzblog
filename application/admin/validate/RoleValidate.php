<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 14:26
 */

namespace app\admin\validate;

use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        'title' => 'require|max:10'
    ];
    protected $field=[
        'title'=>'角色名称'
    ];
}