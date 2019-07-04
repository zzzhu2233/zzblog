<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/4
 * Time: 9:15
 */

namespace app\admin\model;
use think\Model;

class UserInfo extends Model
{
    //在模型中配置自动写入时间戳，没有配置这个的模型，则不会自动写入
    protected $autoWriteTimestamp=true;
}