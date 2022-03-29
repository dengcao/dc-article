<?php
namespace app\index\controller;
use app\index\model\Article as ArticleModel;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\View;
use app\index\model\Category as CategoryModel;
use think\facade\Cache;

class Article
{
    protected $templates_dir;//模板目录

    function __construct()
    {

    }

    public function list($catid="")
    {//文章列表
        if(!is_numeric($catid)){return "分类[ID：".$catid."]不存在";}

        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        //优先读取缓存
        $category_cache_id=md5_plus("category_info_".$catid);
        $category_cache_data = Cache::get($category_cache_id);
        if ($category_cache_data) {
            $category=$category_cache_data;
        } else {
            $category=CategoryModel::where("catid","=",$catid)->findOrEmpty();
            Cache::set($category_cache_id, $category, $web_config["cache_expire"]);
        }

        $page=Request::param("p");
        if(!is_numeric($page)){$page=1;}
        $limit=$category->list_num;//每页显示数

        if($category->type<1){//内部栏目
            //优先读取缓存
            $article_list_cache_id=md5_plus("article_list_".$catid."_".$page."_".$limit);
            $article_list_cache_data = Cache::get($article_list_cache_id);
            if ($article_list_cache_data) {
                $list=$article_list_cache_data;
                $pages = Cache::get($article_list_cache_id."_pages");
            } else {
                $list=Db::name('article');
                $list=$list->alias('a')->leftJoin('category c','a.catid = c.catid');
                $list=$list->alias('a')->field('a.*,c.catname,c.url as caturl')->order(['a.listorder'=>'desc','a.inputtime'=>'desc','a.id'=>'desc']);

                if($category->child==1){
                    $arrchildid_list_arr = CategoryModel::where("catid", "in", $catid)->field("arrchildid")->select()->toArray();
                    $arrchildid_arr = array();
                    foreach ($arrchildid_list_arr as $value) {
                        $arrchildid_arr[] = $value["arrchildid"];
                    }
                    $arrchildid_str = implode(",", $arrchildid_arr);
                    if ($arrchildid_str) {
                        $list = $list->where("a.catid", "in", $arrchildid_str);
                    }
                }else{
                    $list = $list->where("a.catid", "=", $catid);
                }

                $list = $list->where("a.status", "=", 9);
                $list=$list->paginate([
                    'list_rows'=> $limit,//每页数量
                    'page' => $page,//当前页
                ]);
                // 获取分页显示
                //$pages = $list->render();
                $pages = get_cz_pages($list->total(),$limit,1,2);

                Cache::set($article_list_cache_id, $list, $web_config["cache_expire"]);
                Cache::set($article_list_cache_id."_pages", $pages, $web_config["cache_expire"]);
            }
            View::assign([
                'article_list' => $list,
                'pages' => $pages,
            ]);
            $templates_default=$site_style."/list";//默认模板

        }elseif($category->type==2){//外部链接
            redirect($category->url,302);
        }elseif($category->type==1){//单网页
            $templates_default=$site_style."/page";//默认模板
        }

        $web_config=get_web_config();

        $category->keywords_arr=explode(",",$category->keywords);

        View::assign([
            'category' => $category,
            'web_config' => $web_config,
        ]);

        //获取模板
        if($category->style){
            $templates=$category->style;
        }else{
            $templates=$templates_default;
        }

        return View::fetch($templates);
    }

    public function search()
    {//搜索文章
        $keyword=trim(Request::param("keyword",'','filter_sql'));

        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        $page=Request::param("p");
        if(!is_numeric($page)){$page=1;}
        $limit=10;//每页显示数

        //优先读取缓存
        $article_list_cache_id=md5_plus("article_search_".$keyword."_".$page."_".$limit);
        $article_list_cache_data = Cache::get($article_list_cache_id);
        if ($article_list_cache_data) {
            $list=$article_list_cache_data;
            $pages = Cache::get($article_list_cache_id."_pages");
        }elseif($keyword) {
            $list=Db::name('article');
            $list=$list->alias('a')->leftJoin('category c','a.catid = c.catid');
            $list=$list->alias('a')->field('a.*,c.catname,c.url as caturl')->order(['a.inputtime'=>'desc','a.id'=>'desc']);

            $list=$list->where("a.title|a.content|a.keywords|a.description|a.url|a.author|a.copyfrom","like","%".$keyword."%");

            $list = $list->where("a.status", "=", 9);
            $list=$list->paginate([
                'list_rows'=> $limit,//每页数量
                'page' => $page,//当前页
            ]);
            // 获取分页显示
            //$pages = $list->render();
            $pages = get_cz_pages($list->total(),$limit,1,2);

            Cache::set($article_list_cache_id, $list, $web_config["cache_expire"]);
            Cache::set($article_list_cache_id."_pages", $pages, $web_config["cache_expire"]);
        }else{
            $list=array();
            $pages="";
        }

        $web_config=get_web_config();

        View::assign([
            'article_list' => $list,
            'pages' => $pages,
            'keyword' => $keyword,
            'web_config' => $web_config,
        ]);

        return View::fetch($site_style.'/search');
    }

    public function show($id)
    {
        //$id=Request::param("id",'','filter_sql');
        if(!is_numeric($id)){
            caozha_error("参数错误","",1);
        }

        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        //优先读取缓存
        $article_show_cache_id=md5_plus("article_show_".$id);
        $article_show_cache_data = Cache::get($article_show_cache_id);
        if ($article_show_cache_data) {
            $article=$article_show_cache_data;
        } else {
            $article = ArticleModel::where("id", "=", $id)->with('category')->findOrEmpty();
            if ($article->isEmpty()) {
                caozha_error("[ID:" . $id . "]文章不存在。", "", 1);
            } else {
                //$article->hits += 1;
                //$article->save();
                Cache::set($article_show_cache_id, $article, $web_config["cache_expire"]);
            }
        }

        View::assign([
            'web_config' => $web_config,
            'article' => $article,
        ]);

        //获取模板
        if($article->cat_content_style){
            $templates=$article->cat_content_style;
        }else{
            $templates=$site_style."/show";
        }

        // 模板输出
        return View::fetch($templates);
    }

    public function update_hits($id)
    {
        if(!is_numeric($id)){
        return "//参数错误";
        }
        $article = ArticleModel::where("id", "=", $id)->findOrEmpty();
        if ($article->isEmpty()) {
            return "//文章不存在";
        } else {
            $article->hits += 1;
            $article->save();
            return 'document.getElementById("art_hits").innerHTML="'.$article->hits.'";';
        }
    }

    public function cat()//所有分类列表
    {
        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        View::assign([
            'web_config' => $web_config,
        ]);

        //获取模板
        $templates=$site_style."/cat";

        // 模板输出
        return View::fetch($templates);
    }

    public function new()//最新100条
    {
        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        View::assign([
            'web_config' => $web_config,
        ]);

        //获取模板
        $templates=$site_style."/new";

        // 模板输出
        return View::fetch($templates);
    }

    public function test()
    {//演示
        $web_config = get_web_config();
        $site_style = $web_config["site_style"];//默认模板目录

        View::assign([
            'demo_time' => time(),
        ]);
        return View::fetch($site_style.'/test');
    }
}