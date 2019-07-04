<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31
 * Time: 9:03
 */

namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class Blog extends Model
{
    protected $autoWriteTimestamp=true;
    use SoftDelete;
    //显示指定模型对应的表
    //protected $table = 'hy_aaa';
    protected function getTitleAttr($value){
        return mb_substr($value,0,13).'....';
    }
    //创建到User模型的多对一的关联
    public function user(){
        return $this->belongsTo('User','user_id','id');
    }
    //创建到Category模型的多对一的关联
    public function category(){
        return $this->belongsTo('Category','category_id','id');
    }
}