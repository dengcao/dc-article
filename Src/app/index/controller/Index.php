<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://blog.5300.cn）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/dengcao/dc-article   or   Gitee：https://gitee.com/dengzhenhua/dc-article
 */

namespace app\index\controller;

use app\index\model\Links as LinksModel;
use think\facade\Cache;
use think\facade\View;

class Index
{
    public function index()
    {
        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        //优先读取缓存
        $links_show_cache_id=md5_plus("links_show");
        $links_show_cache_data = Cache::get($links_show_cache_id);
        if ($links_show_cache_data) {
            $links=$links_show_cache_data;
        } else {
            $links = LinksModel::where("status", "=", 1)->select();
            Cache::set($links_show_cache_id, $links, $web_config["cache_expire"]);
        }

        View::assign([
            'web_config' => $web_config,
            'links' => $links,
        ]);

        //获取模板
        $templates=$site_style."/index";

        // 模板输出
        return View::fetch($templates);
    }

}
