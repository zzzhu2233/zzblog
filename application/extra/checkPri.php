<?php

namespace app\extra;

use think\Session;
use app\admin\model\RoleUser;
use app\admin\model\RoleAction;
use app\admin\model\Role;
use app\admin\model\Action;

class checkPri
{
    //根据用户获取权限规则
    public static function getActionByUser()
    {
        //根据用户id获取角色id
        $user_id = Session::get('user.id');
        $roles_id = RoleUser::where('user_id', $user_id)
            ->column('role_id');
        //根据角色id获取权限id
        $actions_id = RoleAction::where('role_id', 'in', $roles_id)
            ->column('action_id');
        //根据权限id获取其权限规则
        $rules = Action::where('id', 'in', $actions_id)
            ->column('rule');
        $rules = array_map(function ($v) {
            return strtolower($v);
        }, $rules);
        return $rules;
    }

//验证用户请求的url是否在角色拥有的权限规则中，有的话返回 true，否则返回false
    public static function checkAction($path)
    {

        //判断当前登录用户是否超级管理员，如果是，则直接返回true
        $user_id = Session::get('user.id');
//获取用户的所有角色id
        $roles_id = RoleUser::where('user_id', $user_id)
            ->column('role_id');
        $count = Role::where([
            'id' => ['in', $roles_id],
            'is_super' => 1
        ])->count();

        if ($count > 0) {
            return true;
        }

        //获取用户所属角色的权限规则
        $rules = self::getActionByUser();
        return in_array(strtolower($path), $rules);
    }
}