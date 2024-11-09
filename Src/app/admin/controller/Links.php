<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://blog.5300.cn）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/dengcao/dc-article   or   Gitee：https://gitee.com/dengzhenhua/dc-article
 */

namespace app\admin\controller;

use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\admin\model\Links as LinksModel;

class Links
{
    protected $middleware = [
        'caozha_auth' 	=> ['except' => '' ],//验证是否管理员
    ];

    public function __construct(){
        cz_auth("admin");//检测是否有权限
    }

    public function index()
    {
        $web_config=get_web_config();
        $limit=$web_config["links_limit"];
        if(!is_numeric($limit)){
            $limit=15;//默认显示15条
        }
        View::assign([
            'links_limit'  => $limit
        ]);
        // 模板输出
        return View::fetch('links/index');
    }

    public function add()
    {
        $links_status = Config::get("app.caozha_links_status");
        View::assign([
            'links_status' => $links_status,
        ]);
        // 模板输出
        return View::fetch('links/add');
    }

    public function addSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }
        $edit_data=Request::param('','','filter_sql');//过滤注入
        $edit_data["status"]=isset($edit_data["status"])?$edit_data["status"]:0;

        $links_id = Db::name('links')->insertGetId($edit_data);

        if($links_id>0){
            write_syslog(array("log_content"=>"新增友情链接：".$edit_data["link_name"]."，ID：".$links_id));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"添加成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"添加失败");
        }
        return json($list);
    }

    public function edit()
    {
        $id=Request::param("id",'','filter_sql');
        if(!is_numeric($id)){
            caozha_error("参数错误","",1);
        }
        $links=linksModel::where("id","=",$id)->findOrEmpty();
        if ($links->isEmpty()) {
            caozha_error("[ID:".$id."]友情链接不存在。","",1);
        }else{
            View::assign([
                'links'  => $links
            ]);
        }
        $links_status = Config::get("app.caozha_links_status");
        View::assign([
            'links_status' => $links_status,
        ]);

        // 模板输出
        return View::fetch('links/edit');
    }

    public function editSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }
        $edit_data=Request::param('','','filter_sql');//过滤注入
        $edit_data["status"]=isset($edit_data["status"])?$edit_data["status"]:0;
        $update_field=['link_name','link_url','link_img','listorder','status','remarks'];//允许更新的字段

        $links=LinksModel::where("id","=",$edit_data["id"])->findOrEmpty();
        if ($links->isEmpty()) {//数据不存在
            $update_result=false;
        }else{
            $update_result=$links->allowField($update_field)->save($edit_data);
        }

        if($update_result){
            write_syslog(array("log_content"=>"修改友情链接：".$edit_data["link_name"]."，ID：".$edit_data["id"]));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"保存成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"保存失败");
        }
        return json($list);
    }

    public function view()
    {
        $id=Request::param("id",'','filter_sql');
        if(!is_numeric($id)){
            caozha_error("参数错误","",1);
        }
        $links=LinksModel::where("id","=",$id)->withAttr('status', function($value) {
            $status = Config::get("app.caozha_links_status");
            return $status[$value];
        })->withAttr('link_img', function($value) {
            return "<img src='".$value."' border=0 height=30>";
        })->findOrEmpty();
        if ($links->isEmpty()) {
            caozha_error("[ID:".$id."]友情链接不存在。","",1);
        }else{
            View::assign([
                'links'  => $links
            ]);
        }
        // 模板输出
        return View::fetch('links/view');
    }

    public function get()//获取管理员数据
    {
        $page=Request::param("page");
        if(!is_numeric($page)){$page=1;}
        $limit=Request::param("limit");
        if(!is_numeric($limit)){
            $web_config=get_web_config();
            $limit=$web_config["links_limit"];
            if(!is_numeric($limit)){
                $limit=15;//默认显示15条
            }
        }
        $list=LinksModel::order(['listorder'=>'desc','id'=>'desc'])->withAttr('status', function($value) {
        $status = Config::get("app.caozha_links_status");
        return $status[$value];
    })->withAttr('link_img', function($value) {
        return "<img src='".$value."' border=0 height=30>";
    })->withAttr('link_url', function($value) {
        return "<a href='".$value."' target=_blank>".$value."</a>";
    });
        $keyword=Request::param("keyword",'','filter_sql');
        if($keyword){
            $list=$list->whereOr([ ["link_name","like","%".$keyword."%"],["link_url","like","%".$keyword."%"],["link_img","like","%".$keyword."%"] ]);
        }
        $list=$list->paginate([
            'list_rows'=> $limit,//每页数量
            'page' => $page,//当前页
        ]);
        return json($list);
    }

    public function delete()//删除数据
    {
        //执行删除
        $id=Request::param("id",'','filter_sql');
        $del_num=0;
        if($id){
            $del_num=LinksModel::where("id","in",$id)->delete();
        }
        if($del_num>0){
            write_syslog(array("log_content"=>"删除友情链接(ID)：".$id));//记录系统日志
            $list=array("code"=>1,"del_num"=>$del_num);
        }else{
            $list=array("code"=>0,"del_num"=>0);
        }

        return json($list);
    }

}
