<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/dc-article   or   Gitee：https://gitee.com/caozha/dc-article
 */

namespace app\admin\model;
use think\Model;
class Administrators extends Model
{
   // protected $connection="mysql";

    //获取器，把is_enabled变成数组{val,text}
//    public function getIsEnabledAttr($value)
//    {
//        $is_enabled = [0=>'<i class="layui-icon layui-icon-close hese"></i>',1=>'<i class="layui-icon layui-icon-ok olivedrab"></i>'];
//        return ['val' => $value, 'text' => $is_enabled[$value]];
//    }

    //一对一关联
    public function roles() {
        return $this->hasOne(Roles::class,"role_id","role_id")->bind([
            'role_name'	=> 'role_name',
        ]);
    }
}