<?php
/**
 * 源码名：dc-article
 * Copyright © 邓草 （官网：http://blog.5300.cn）
 * 基于木兰宽松许可证 2.0（Mulan PSL v2）免费开源，您可以自由复制、修改、分发或用于商业用途，但需保留作者版权等声明。详见开源协议：http://license.coscl.org.cn/MulanPSL2
 * dc-article (Software Name) is licensed under Mulan PSL v2. Please refer to: http://license.coscl.org.cn/MulanPSL2
 * Github：https://github.com/dengcao/dc-article   or   Gitee：https://gitee.com/dengzhenhua/dc-article
 */

namespace app\admin\controller;

use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\admin\model\Block as BlockModel;

class Block
{
    protected $middleware = [
        'caozha_auth' 	=> ['except' => '' ],//验证是否管理员
    ];

    public function __construct(){
        cz_auth("block");//检测是否有权限
    }

    public function index()
    {
        $web_config=get_web_config();
        $limit=$web_config["block_limit"];
        if(!is_numeric($limit)){
            $limit=15;//默认显示15条
        }
        View::assign([
            'block_limit'  => $limit
        ]);
        // 模板输出
        return View::fetch('block/index');
    }

    public function add()
    {
        // 模板输出
        return View::fetch('block/add');
    }

    public function addSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }
        $edit_data=Request::param('','','filter_sql');//过滤注入
        $edit_data["is_enabled"]=isset($edit_data["is_enabled"])?$edit_data["is_enabled"]:0;
        $edit_data["updatetime"]=date("Y-m-d H:i:s",time());

        //检测是否存在
        $block_check=BlockModel::where([['marker','=',$edit_data["marker"]] ])->findOrEmpty();
        if (!$block_check->isEmpty()) {//数据存在
            return json(array("code"=>0,"update_num"=>0,"msg"=>"已存在相同的标识符，请换一个。"));
        }

        $block_id = Db::name('block')->insertGetId($edit_data);

        if($block_id>0){
            write_syslog(array("log_content"=>"新增碎片：".$edit_data["marker"]."，ID：".$block_id));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"添加成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"添加失败");
        }
        return json($list);
    }

    public function edit()
    {
        $block_id=Request::param("block_id",'','filter_sql');
        if(!is_numeric($block_id)){
            caozha_error("参数错误","",1);
        }
        $block=BlockModel::where("block_id","=",$block_id)->findOrEmpty();
        if ($block->isEmpty()) {
            caozha_error("[ID:".$block_id."]碎片不存在。","",1);
        }else{
            View::assign([
                'block'  => $block
            ]);
        }
        // 模板输出
        return View::fetch('block/edit');
    }

    public function editSave()
    {
        if(!Request::isAjax()){
            // 如果不是AJAX
            return result_json(0,"error");
        }
        $edit_data=Request::param('','','filter_sql');//过滤注入
        $edit_data["is_enabled"]=isset($edit_data["is_enabled"])?$edit_data["is_enabled"]:0;
        $edit_data["updatetime"]=date("Y-m-d H:i:s",time());
        $update_field=['classid','title','marker','content','updatetime','is_enabled','block_remarks'];//允许更新的字段

        //检测是否存在
        $block_check=BlockModel::where([ ['block_id','<>',$edit_data["block_id"]], ['marker','=',$edit_data["marker"]] ])->findOrEmpty();
        if (!$block_check->isEmpty()) {//数据存在
            return json(array("code"=>0,"update_num"=>0,"msg"=>"已存在相同的标识符，请换一个。"));
        }

        $block=BlockModel::where("block_id","=",$edit_data["block_id"])->findOrEmpty();
        if ($block->isEmpty()) {//数据不存在
            $update_result=false;
        }else{
            $update_result=$block->allowField($update_field)->save($edit_data);
        }

        if($update_result){
            write_syslog(array("log_content"=>"修改碎片：".$edit_data["title"]."，标识符：".$edit_data["marker"]."，ID：".$edit_data["block_id"]));//记录系统日志
            $list=array("code"=>1,"update_num"=>1,"msg"=>"保存成功");
        }else{
            $list=array("code"=>0,"update_num"=>0,"msg"=>"保存失败");
        }
        return json($list);
    }

    public function view()
    {
        $block_id=Request::param("block_id",'','filter_sql');
        if(!is_numeric($block_id)){
            caozha_error("参数错误","",1);
        }
        $block=BlockModel::where("block_id","=",$block_id)->withAttr('is_enabled', function($value) {
            $is_enabled = [0=>'<i class="layui-icon layui-icon-close hese"></i>',1=>'<i class="layui-icon layui-icon-ok olivedrab"></i>'];
            return $is_enabled[$value];
        })->findOrEmpty();
        if ($block->isEmpty()) {
            caozha_error("[ID:".$block_id."]碎片不存在。","",1);
        }else{
            $block['block_remarks']=str_replace("\n","<br>",$block['block_remarks']);
            View::assign([
                'block'  => $block
            ]);
        }
        // 模板输出
        return View::fetch('block/view');
    }

    public function get()//获取管理员数据
    {
        $page=Request::param("page");
        if(!is_numeric($page)){$page=1;}
        $limit=Request::param("limit");
        if(!is_numeric($limit)){
            $web_config=get_web_config();
            $limit=$web_config["block_limit"];
            if(!is_numeric($limit)){
                $limit=15;//默认显示15条
            }
        }
        $list=BlockModel::order('block_id', 'desc')->withAttr('is_enabled', function($value) {
        $is_enabled = [0=>'<i class="layui-icon layui-icon-close hese"></i>',1=>'<i class="layui-icon layui-icon-ok olivedrab"></i>'];
        return $is_enabled[$value];
    });
        $keyword=Request::param("keyword",'','filter_sql');
        if($keyword){
            $list=$list->whereOr([ ["title","like","%".$keyword."%"],["marker","like","%".$keyword."%"],["content","like","%".$keyword."%"],["block_remarks","like","%".$keyword."%"] ]);
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
        $block_id=Request::param("block_id",'','filter_sql');
        $del_num=0;
        if($block_id){
            $del_num=BlockModel::where("block_id","in",$block_id)->delete();
        }
        if($del_num>0){
            write_syslog(array("log_content"=>"删除碎片(ID)：".$block_id));//记录系统日志
            $list=array("code"=>1,"del_num"=>$del_num);
        }else{
            $list=array("code"=>0,"del_num"=>0);
        }

        return json($list);
    }

}
