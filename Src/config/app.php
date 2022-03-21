<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/dc-article   or   Gitee：https://gitee.com/caozha/dc-article
 */

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [

    // 应用映射（自动多应用模式有效）
    'app_map'          => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [
        'admin.caozha.com' => 'admin',  // 绑定后台域名
        '*'                => 'index', // 泛域名绑定到index应用
    ],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => [],

];
