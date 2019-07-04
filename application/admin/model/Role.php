<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/6
 * Time: 14:09
 */

namespace app\admin\model;

use think\Model;

class Role extends Model
{
    //在模型中配置自动写入时间戳，没有配置这个的模型，则不会自动写入
    protected $autoWriteTimestamp=true;
    public function getIsSuperAttr($value)
    {
        $newvalue = '否';
        if ($value==1){
            $newvalue='是';
        }
        return $newvalue;
    }
}