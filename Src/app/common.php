<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/dc-article   or   Gitee：https://gitee.com/caozha/dc-article
 */

// 应用公共文件

/**
 * 获取评论的表情包
 * @return array
 */
function comment_face(){
    $cmt_faces = [
        ["微笑", "wx.gif"],
        ["鄙视", "bs.gif"],
        ["闭嘴", "bz.gif"],
        ["吃惊", "cj.gif"],
        ["酷", "cool.gif"],
        ["呲牙", "cy.gif"],
        ["鼓掌", "gz.gif"],
        ["流汗", "han.gif"],
        ["哈欠", "hq.gif"],
        ["害羞", "hx.gif"],
        ["可爱", "ka.gif"],
        ["泪", "lei.gif"],
        ["难过", "ng.gif"],
        //["高手", "q.gif"],
        ["示爱", "sa.gif"],
        ["衰", "shuai.gif"],
        ["憨笑", "hanx.gif"],
        ["吐血", "tux.gif"],
        ["偷笑", "tx.gif"],
        ["斜眼笑", "xyx.gif"],
        ["笑哭", "xk.gif"],
        ["色", "se.gif"],
        ["晕", "y.gif"],
        ["折磨", "zm.gif"],
        ["坏笑", "67.gif"],
        ["撇嘴", "2.gif"],
        ["睡", "8.gif"],
        ["尴尬", "10.gif"],
        ["发怒", "11.gif"],
        ["调皮", "12.gif"],
        ["吐", "18.gif"],
        ["白眼", "21.gif"],
        ["困", "24.gif"],
        ["惊恐", "25.gif"],
        ["大兵", "28.gif"],
        ["奋斗", "29.gif"],
        ["疑问", "30.gif"],
        ["嘘", "31.gif"],
        ["敲打", "35.gif"],
        ["再见", "36.gif"],
        ["猪头", "40.gif"],
        ["抱抱", "41.gif"],
        ["蛋糕", "42.gif"],
        //["闪电", "43.gif"],
        ["炸弹", "44.gif"],
        //["刀", "45.gif"],
        ["便便", "47.gif"],
        ["咖啡", "48.gif"],
        ["饭", "49.gif"],
        ["玫瑰", "50.gif"],
        ["凋谢", "51.gif"],
        ["爱心", "52.gif"],
        ["心碎", "53.gif"],
        //["太阳", "55.gif"],
        //["月亮", "56.gif"],
        ["强", "57.gif"],
        ["弱", "58.gif"],
        ["握手", "59.gif"],
        ["抠鼻", "64.gif"],
        ["委屈", "72.gif"],
        ["阴险", "74.gif"],
        ["亲亲", "75.gif"],
        ["可怜", "77.gif"],
        ["菜刀", "78.gif"],
        ["啤酒", "79.gif"],
        ["抱拳", "84.gif"],
        ["勾引", "85.gif"],
        //["OK", "90.gif"],
        ["蜡烛", "102.gif"],
        ["鞭炮", "126.gif"],
        ["红包", "105.gif"],
        ["福", "125.gif"],
    ];
    return $cmt_faces;
}

//应用的名称及版本
$GLOBALS["caozha_common_config"] = [
    "name" => "dc-article",
    "version" => "1.2.0",
    "gitee" => "caozha/dc-article",
    "github" => "cao-zha/dc-article",
];

//caozha-admin 程序名称及版本，用于标识和升级，勿删改
$GLOBALS["caozha_admin_sys"] = array(
    "name" => "caozha-admin",
    "version" => "1.8.2",
    "url" => "https://gitee.com/caozha/caozha-admin",
);

/**
 * 获取应用入口之前的目录，格式如：/public/或/
 * @return string
 */
function get_cz_path(){
    $cz_path=substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'], '/')+1);
    if(substr($cz_path, 0, 11)=="/index.php/" || substr($cz_path, 0, 11)=="/admin.php/"){
        $cz_path="/";
    }
    return $cz_path;
}

/**
 * 获取系统应用的名字
 * @return string
 */
function get_cz_name(){
    global $caozha_common_config;
    return $caozha_common_config["name"];
}

/**
 * 获取系统应用的版本号
 * @return string
 */
function get_cz_version(){
    global $caozha_common_config;
    return $caozha_common_config["version"];
}

/**
 * 分页
 * @return string
 */
function get_cz_pages($total_rows,$list_rows,$show_style=1,$css_style=1){
    $css_styles=array(
        "s1"=>'<style type="text/css">
#cz_page{font: 15px "Microsoft YaHei", Arial, Helvetica, sans-serif;text-align:center;}
#cz_page span{margin:0px 3px;}
#cz_page a{margin:5px 3px;border:1px solid #F7F7F7;padding:5px 10px; text-decoration:none;display:inline-block;color:#666;background:#F7F7F7}
#cz_page a.now_page,#cz_page a:hover{color:#fff;background:#1487f4}
#goto_page{padding:4px 0px;}
</style>',
        "s2"=>'<style type="text/css">
#cz_page{font: 15px "Microsoft YaHei", Arial, Helvetica, sans-serif;text-align:center;}
#cz_page span{margin:0px 3px;}
#cz_page a{margin:5px 3px;border:1px solid #F7F7F7;padding:5px 10px; text-decoration:none;display:inline-block;color:#666;background:#F7F7F7}
#cz_page a.now_page,#cz_page a:hover{color:#fff;background:#000}
#goto_page{padding:4px 0px;}
</style>',
    );
    include 'common/class/page.class.php';
    $options = array(
        'total_rows' => $total_rows,
        'list_rows'  => $list_rows,
    );
    $pages = new page($options);
    $pages_html="<div id=\"cz_page\">".$pages->show($show_style)."</div>"; //打印样式,1,2,3,4
    return $css_styles["s".$css_style].$pages_html;
}


/**
 * 获取模板文件数组
 * @return array
 */
function getTemplates(){
    $tpl_path=base_path()."index/view/";
    $tpl_file_arr=array();
    if(is_dir($tpl_path)){
        $tpl_dir=scandir($tpl_path);//遍历目录，获取文件名
        foreach($tpl_dir as $value){
            if(substr($value,0,1)=="."){//判断是否目录，去除目录
                continue;
            }
            $tpl_file=scandir($tpl_path.$value."/");//遍历目录，获取文件名
            foreach($tpl_file as $value_file){
                if(substr($value_file,0,1)=="."){//判断是否目录，去除目录
                    continue;
                }
                $tpl_file_arr[]=$value."/".str_ireplace(".html","",$value_file);
            }
        }
    }
    return $tpl_file_arr;
}

/**
 * 获取模板目录数组
 * @return array
 */
function getTemplatesDir(){
    $tpl_path=base_path()."index/view/";
    $tpl_arr=array();
    if(is_dir($tpl_path)){
        $tpl_dir=scandir($tpl_path);//遍历目录，获取文件名
        foreach($tpl_dir as $value){
            if(substr($value,0,1)=="."){//判断是否目录，去除目录
                continue;
            }
            $tpl_arr[]=str_ireplace(".html","",$value);
        }
    }
    return $tpl_arr;
}


/**
     * Url生成
     * @param string      $url    路由地址
     * @param array       $vars   变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return UrlBuild
     */
    function dc_url(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        if(substr($url, 0, 6)=="index/"){
            $url=substr($url, 5);
        }elseif(substr($url, 0, 6)=="admin/"){
            $url=substr($url, 5);
        }elseif(substr($url, 0, 7)=="/index/"){
            $url=substr($url, 6);
        }elseif(substr($url, 0, 7)=="/admin/"){
            $url=substr($url, 6);
        }
        return url($url,$vars, $suffix, $domain);
    }