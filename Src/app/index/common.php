<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://caozha.com）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/cao-zha/dc-article   or   Gitee：https://gitee.com/caozha/dc-article
 */

error_reporting(0);//解决php8.1时错误

// 应用公共文件

use app\index\model\Category as CategoryModel;
use think\facade\Db;
use think\facade\Request;
use app\index\model\WebConfig as WebConfigModel;
use think\facade\Cache;


/**
 * 过滤参数，防SQL注入
 * @param string $str 接受的参数
 * @return string
 */
function filter_sql($str)
{
    $farr = array(
        //"/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
        "/insert into|drop table|truncate|delete from/is"
    );
    $str = preg_replace($farr, '', $str);
    return trim(addslashes($str));
}

/**
 * 过滤接受的参数或者数组,如$_GET,$_POST
 * @param array|string $arr 接受的参数或者数组
 * @return array|string
 */
function filter_sql_arr($arr)
{
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            $arr[$k] = filter_sql($v);
        }
    } else {
        $arr = filter_sql($arr);
    }
    return $arr;
}

/**
 * 获取客户端IP
 * @return string
 */
function getip()
{ //获取客户端IP
    if (isset($_SERVER["HTTP_CDN_SRC_IP"])) { //获取网宿CDN真实客户IP
        return replace_ip($_SERVER["HTTP_CDN_SRC_IP"]);
    }
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { //获取网宿、阿里云真实客户IP，参考：https://help.aliyun.com/knowledge_detail/40535.html
        return replace_ip($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }
    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    if (isset($_SERVER["HTTP_X_FORWARDED"])) {
        return $_SERVER["HTTP_X_FORWARDED"];
    }
    if (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    }
    if (isset($_SERVER["HTTP_FORWARDED"])) {
        return $_SERVER["HTTP_FORWARDED"];
    }
    $httpip = $_SERVER['REMOTE_ADDR'];
    if (!preg_match("/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/", $httpip)) {
        $httpip = "127.0.0.1";
    }
    return $httpip;
}

/**
 * 拆分代理IP
 * @return string
 */
function replace_ip($ip)
{

    if (!$ip) {
        return "";
    }

    $httpip_array = explode(",", $ip);

    if ($httpip_array[0]) {

        return $httpip_array[0];

    } else {

        return $ip;

    }

}

/**
 *md5加强型，防止破解
 * @param string $str 点确定返回的URL
 * @return string
 */
function md5_plus($str)
{
    return md5("caozha.com|" . md5($str));
}

/**
 * 模板标签库 获取文章列表
 * @param json $tag 标签属性
 * @return object
 */
function template_get_article($tag)
{
    $tag = filter_sql_arr(json_decode($tag, true));
    $limit = explode(",", $tag['limit']);

    //优先读取缓存
    $cache_id = md5_plus("template_get_article_" . implode("-", $tag));
    $template_cache_data = Cache::get($cache_id);
    if ($tag['iscache'] == 1 && $template_cache_data) {
        return $template_cache_data;
    } else {

        $list = Db::name('article');
        $list = $list->alias('a')->leftJoin('category c', 'a.catid = c.catid');
        if (empty($tag['fields'])) {
            $fields = "a.*,";
        } else {
            $fields_arr = explode(",", $tag['fields']);
            $fields = "";
            foreach ($fields_arr as $value) {
                $fields .= "a." . $value . ",";
            }
        }
        $list = $list->alias('a')->field($fields . 'c.catname as cat_catname,c.image as cat_image,c.description as cat_description,c.url as cat_url');

        $attr_arr = ['islink', 'iscomment', 'isreco', 'ishot', 'istop', 'status'];
        foreach ($attr_arr as $value) {
            if ($tag[$value] >= 0) {
                $list = $list->where("a." . $value, "=", intval($tag[$value]));
            }
        }

        if ($tag['isthumb'] == 1) {//是否带缩略图
            $list = $list->where("a.thumb", "<>", "");
        }

        if ($tag['catid'] != 0) {//指定分类ID下的文章
            $arrchildid_list_arr = CategoryModel::where("catid", "in", $tag['catid'])->field("arrchildid")->select()->toArray();
            $arrchildid_arr = array();
            foreach ($arrchildid_list_arr as $value) {
                $arrchildid_arr[] = $value["arrchildid"];
            }
            $arrchildid_str = implode(",", $arrchildid_arr);
            if ($arrchildid_str) {
                $list = $list->where("a.catid", "in", $arrchildid_str);
            }
        }

        if ($tag['orderby']) {//排序
            $orderby_list_arr = explode(",", $tag['orderby']);
            $orderby_arr = array();
            foreach ($orderby_list_arr as $value) {
                $value_arr = explode(" ", $value);
                $orderby_arr["a." . $value_arr[0]] = $value_arr[1];
            }
            $list = $list->order($orderby_arr);
        }

        $list = $list->limit($limit[0], $limit[1])->select();

        $web_config = get_web_config();
        Cache::set($cache_id, $list, $web_config["cache_expire"]);
        return $list;

    }
}


/**
 * 模板标签库 获取分类列表
 * @param json $tag 标签属性
 * @return object
 */
function template_get_category($tag)
{
    $tag = filter_sql_arr(json_decode($tag, true));

    $limit = explode(",", $tag['limit']);

    //优先读取缓存
    $cache_id = md5_plus("template_get_category_" . implode("-", $tag));
    $template_cache_data = Cache::get($cache_id);
    if ($tag['iscache'] == 1 && $template_cache_data) {
        return $template_cache_data;
    } else {

        $list = Db::name('category');
        if (is_numeric($tag['parentid'])) {
            $list = $list->where("parentid", "=", intval($tag['parentid']));
        } else {
            $list = $list->where("parentid", "in", intval($tag['parentid']));
        }
        $attr_arr = ['type', 'modelid', 'child', 'ismenu'];
        foreach ($attr_arr as $value) {
            if ($tag[$value] >= 0) {
                $list = $list->where($value, "=", intval($tag[$value]));
            }
        }

        if ($tag['fields']) {
            $list = $list->field($tag['fields']);
        }

        if ($tag['orderby']) {//排序
            $orderby_list_arr = explode(",", $tag['orderby']);
            $orderby_arr = array();
            foreach ($orderby_list_arr as $value) {
                $value_arr = explode(" ", $value);
                $orderby_arr[$value_arr[0]] = $value_arr[1];
            }
            $list = $list->order($orderby_arr);
        }
        $list = $list->limit($limit[0], $limit[1])->select();
        $web_config = get_web_config();
        Cache::set($cache_id, $list, $web_config["cache_expire"]);
        return $list;

    }
}


/**
 * 模板标签库 获取任意数据表数据，万能标签
 * @param json $tag 标签属性
 * @return object
 */
function template_get($tag)
{
    $tag = filter_sql_arr(json_decode($tag, true));

    $cz_prefix = config('database.connections.mysql.prefix');//数据表前缀
    if (empty($tag['fields'])) {
        $tag['fields'] = "*";
    }
    if (!empty($tag['where'])) {
        $tag['where'] = " where " . str_ireplace("\\'","'",$tag['where']);//暂时允许出现'符号
    }
    if ($tag['orderby']) {//排序
        $tag['orderby'] = " order by " . $tag['orderby'];
    }

    //优先读取缓存
    $cache_id = md5_plus("template_get_" . implode("-", $tag));
    $template_cache_data = Cache::get($cache_id);
    if ($tag['iscache'] == 1 && $template_cache_data) {
        return $template_cache_data;
    } else {

        $list = Db::query("select " . $tag['fields'] . " from `" . $cz_prefix . $tag['datatable'] . "` " . $tag['where'] . $tag['orderby'] . " limit " . $tag['limit'] . ";");
        $web_config = get_web_config();
        Cache::set($cache_id, $list, $web_config["cache_expire"]);
        return $list;

    }
}


/**
 * 模板标签库 获取碎片数据
 * @param json $tag 标签属性
 * @return string
 */
function template_getBlock($tag)
{
    $tag = filter_sql_arr(json_decode($tag, true));

    if (empty($tag['marker'])) {
        return '';
    }
    $cz_prefix = config('database.connections.mysql.prefix');//数据表前缀
    $tag['where'] = " where is_enabled=1 and marker='".$tag['marker']."'";

    //优先读取缓存
    $cache_id = md5_plus("template_getBlock_" . implode("-", $tag));
    $template_cache_data = Cache::get($cache_id);
    if ($tag['iscache'] == 1 && $template_cache_data) {
        return $template_cache_data;
    } else {

        $block_content_data="";
        $list = Db::query("select `content` from `" . $cz_prefix . "block` " . $tag['where'] . " limit 0,1;");
        foreach ($list as $data) {
            $block_content_data=$data["content"];
        }
        if ($tag['is_decode']==1 && $block_content_data!=""){
            $block_content_data=html_entity_decode($block_content_data);//HTML实体转换为字符
        }
        if ($tag['is_strip']==1 && $block_content_data!=""){
            $block_content_data=strip_tags($block_content_data,$tag['allow_html']);
        }
        $web_config = get_web_config();
        Cache::set($cache_id, $block_content_data, $web_config["cache_expire"]);
        return $block_content_data;

    }
}


/**
 * 转换标签属性值为变量
 * @param string attr 属性值
 * @return string
 */
function attr_to_variable($attr)
{
    if (!$attr) {
        return "";
    } else {
        $attr = trim($attr);
    }
    if (mb_substr($attr, 0, 1) == "$") {
        return "'." . $attr . ".'";
    } else {
        return $attr;
    }
}


/**
 * 对象转数组
 * @param object $obj 对象
 * @return array
 */
function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }
    return $obj;
}

/**
 *获取系统设置数据
 * @return array
 */
function get_web_config()
{
    $web_config_data = Cache::get('web_config');
    if ($web_config_data) {
        return $web_config_data;
    } else {
        $web_config = WebConfigModel::where("id", ">=", 1)->limit(1)->findOrEmpty();
        if ($web_config->isEmpty()) {
            return array();
        } else {
            $web_config_data = object_to_array($web_config->web_config);
            Cache::set('web_config', $web_config_data, $web_config_data["cache_expire"]);
            return $web_config_data;
        }
    }
}

/**
 *转换文章URL
 * @param string url 文章URL链接
 * @param string id 文章ID
 * @return string
 */
function art_url($url, $id)
{
    if ($url) {
        return $url;
    } else {
        $web_config = get_web_config();
        //return $web_config["site_url"] . dc_url('/index/article/show/id/') . $id;
        return $web_config["site_url"] . '/show/' . $id;
    }
}

/**
 *转换文章分类URL
 * @param string url 分类URL链接
 * @param string catid 分类ID
 * @return string
 */
function art_cat_url($url, $catid)
{
    if ($url) {
        return $url;
    } else {
        $web_config = get_web_config();
        //return $web_config["site_url"] . dc_url('/index/article/list/catid/') . $catid;
        return $web_config["site_url"] . '/list/' . $catid;
    }
}

/**
 *获取文章分类导航
 * @param string catid 分类ID
 * @return string
 */
function art_get_nav($catid)
{
    if (!is_numeric($catid)) {
        return "";
    } else {
        //优先读取缓存
        $cache_id = md5_plus("art_get_nav_" . $catid);
        $art_get_nav_data = Cache::get($cache_id);
        if ($art_get_nav_data) {
            return $art_get_nav_data;
        } else {

            $category = categoryModel::where("catid", "=", $catid)->field('arrparentid')->findOrEmpty();
            if ($category->isEmpty()) {
                return "";
            } else {

                $arrparentid=$category->arrparentid;
                $arrparentid.=",".$catid;
                $arrparentid_arr = explode(",", $arrparentid);
                $nav_arr=array();
                foreach ($arrparentid_arr as $value) {
                    if($value==0){continue;}
                    $nav_arr[$value]=array();
                }

                $list = Db::name('category')->where("catid","in",$arrparentid);
                $list = $list->field("catid,catname,url")->select()->toArray();
                foreach ($list as $value) {
                    $nav_arr[$value["catid"]]=$value;
                }

                $nav_html="";
                foreach ($nav_arr as $value) {
                    $nav_html.=' > <a href="'.art_cat_url($value["url"],$value["catid"]).'">'.$value["catname"].'</a>';
                }

                $web_config = get_web_config();
                Cache::set($cache_id, $nav_html, $web_config["cache_expire"]);
                return $nav_html;

            }
        }
    }
}

