<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://blog.5300.cn）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/dengcao/dc-article   or   Gitee：https://gitee.com/dengzhenhua/dc-article
 */

namespace app\index\model;
use think\Model;
class Article extends Model
{
   // protected $connection="mysql";
    public function category() {
        return $this->hasOne(Category::class,"catid","catid")->bind([
            'cat_type'	=> 'type',
            'cat_modelid'	=> 'modelid',
            'cat_parentid'	=> 'parentid',
            'cat_arrparentid'	=> 'arrparentid',
            'cat_catname'	=> 'catname',
            'cat_catid'	=> 'catid',
            'cat_image'	=> 'image',
            'cat_ismenu'	=> 'ismenu',
            'cat_style'	=> 'style',
            'cat_content_style'	=> 'content_style',
            'cat_description'	=> 'description',
            'cat_url'	=> 'url',
            'cat_content'	=> 'content',
            'cat_letter'	=> 'letter',
            'cat_list_num'	=> 'list_num',
        ]);
    }
}