<?php
/**
 * 源码名：caozha-getimg（图片获取器，可以获取远程经防盗的图片，绕过防盗检测）
 * Copyright © 邓草 （官网：http://blog.5300.cn）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * caozha-getimg (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/dengcao/caozha-getimg   or   Gitee：https://gitee.com/dengzhenhua/caozha-getimg
 *
 *  * 使用方法演示：https://caozha.com/git/demo/getimg/img.php?url=远程图片URL    ------请不要直接使用，仅演示。
 */

$url=$_GET["url"];
get_img($url);

/**
 * 获取远程图片
 * @param $url 图片URL
 */
function get_img($url){
    if(substr($url,0,5)=="http/"){
        $url="http://".substr($url,5);
    }elseif(substr($url,0,6)=="https/"){
        $url="https://".substr($url,6);
    }else{
        $url="http://".$url;
    }
    $ext=pathinfo($url,PATHINFO_EXTENSION);
    $ext_arr = explode("?",$ext);
    $ext=$ext_arr[0];
    if($ext=='jpg' || $ext=='gif' || $ext=='png' || $ext=='jpeg' || $ext=='bmp'){
        header('Content-type: image/'.$ext);
    }else{
        header('Content-type: image/jpg');
    }
    echo file_get_contents($url);
}