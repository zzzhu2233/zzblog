<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1
 * Time: 9:09
 */

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class User extends Model
{

    use SoftDelete;
    //在模型中配置自动写入时间戳，没有配置这个的模型，则不会自动写入
    protected $autoWriteTimestamp=true;
    //指定需要自动完成的字段名称
    protected $insert=['img','pwd'];

    //在 $insert 中指定了 img字段，则在插入数据时，就会自动执行下面的方法
    public function setImgAttr(){
        return 'images/assets/1.jpg';
    }
    public function getImgAttr($value)
    {
        return config('domain').'static/' .$value;
    }
    public function setPwdAttr($value){
        return md5($value);
    }

    //创建到UserInfo 模型的一对一关系
    public function userInfo(){
        return $this->hasOne('UserInfo','user_id','id')
            ->field('user_id,realname,address,tell');//field 限制的是关联模型中的字段
    }
    //创建到Blog模型的一对多关系
    public function Blog(){
        return $this->hasMany('Blog','user_id','id');
    }
}