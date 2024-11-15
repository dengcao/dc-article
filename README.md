# dc-article 文章内容管理系统 1.4.1

dc-article是一个通用的文章内容管理系统，基于开源的caozha-admin开发，采用前后端分离的模板和标签化方式，支持文章内容管理、栏目分类管理、评论管理、友情链接管理、碎片管理、远程图片获取器等功能。可以使用本系统很轻松地架构新闻类网站、文章类网站、图片展示类网站或个人博客网站。系统特点：易上手，零门槛，拿来即用，界面清爽极简，极便于二次开发。

## 系统功能

1、系统设置

2、管理员管理

3、权限组管理

4、系统日志

5、后台功能地图

6、文章内容管理

7、栏目分类管理（支持无限级别分类）

8、评论管理（支持盖楼评论，支持设置屏蔽词、验证码、是否需审核等，可整合到任何场景。可以自动适配电脑、平板和手机等不同客户端。）

9、友情链接管理

10、碎片管理

11、整合了百度UEditor（编辑器）、caozha-getimg（远程图片获取器，可以获取远程经防盗的图片，绕过防盗检测）等各种常用插件，可去演示页面查看。

12、系统采用了缓存机制，加快访问速度。所以后台添加、修改或更新了文章和设置后，必须点击后台右上角的“回收站”按钮，清空缓存。不清空缓存，前台页面不会更新。（当然您也可以在后台“系统设置”里关闭页面缓存。）

13、提供了两套不同的风格，也可以自己设计更多的风格。


主要提供了以上基础功能，您可以在此基础上拓展和开发出不同的应用。


## 安装使用


**开发环境**

本人开发此系统使用的本地环境是：[phpstudy8.1集成工具（已集成php8.0.14nts/php8.1.1nts，有需要点击下载）](https://gitee.com/dengzhenhua/php8.0-8.1-for-phpstudy)，phpMyAdmin 4.8.5，Apache2.4.39（或Nginx1.15.11），PHP8.0.14，MySQL5.7.26。

事实上，您不需要使用跟以上完全一致的环境也可以正常运行本系统，理论上只要PHP>=8.0即可。如有不兼容，建议模拟本环境测试，并欢迎您提建议和反馈BUG。


**快速安装**

1、PHP版本：必须PHP8.0以上。

2、上传目录/Src/内所有源码到服务器，并设置网站的根目录指向运行目录/public/。（此为ThinkPHP6.0的要求）

3、将/Database/目录里的.sql文件导入到MYSQL数据库。

4、修改文件/config/database.php，配置您的数据库信息（如果测试时启用了/.env，还需要修改文件/.env，系统会优先使用此配置文件）。

5、后台访问地址：http://您的域名/admin.php   (账号：dengcao   密码：123456)

6、文章系统采用了缓存机制，所以后台添加、修改或更新了文章和设置后，必须点击后台右上角的“回收站”按钮，清空缓存。不清空缓存，前台页面不会更新。（当然您也可以在后台“系统设置”里关闭页面缓存。）


**伪静态设置**

1、ThinkPHP框架必须在运行目录下设置伪静态才能正常访问，否则会显示404错误。

2、如果您使用的是Apache，伪静态设置为（.htaccess）：


```
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

RewriteRule ^getimg/(.*) get_img/index\.php\?url=$1

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
#RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
</IfModule>
```



3、如果您使用的是Nginx，以wdCP和宝塔Linux面板为例，伪静态设置为：


```
    index index.php;
    rewrite ^/getimg/(.*)$ /get_img/index.php?url=$1  last;
    if (!-e $request_filename) {
       rewrite  ^(.*)$  /index.php?s=/$1  last;
       break;
    }
```




4、在网站运行目录（/public/）下，有两个文件：.htaccess和nginx.htaccess，分别是Apache和Nginx的伪静态文件，您可以直接拿来使用。


**远程图片获取器 使用说明**

caozha-getimg是一款图片获取器，可以获取远程经防盗的图片，绕过防盗检测。

直接调用方式：
http://（域名）/getimg/https（或http，根据远程图片决定）/(远程图片URL，即除https://部分的URL)

参考实例：http://www.域名.com/getimg/https/www.baidu.com/img/PCtm_d9c8750bed0b3c7d089fa7d55720d6cf.png


## 开发手册

### 1、模板制作（标签调用）

**（1）文章列表标签。**

可以获取任意文章列表的数据。

**举例：**


```
{dc:article name='art' catid='' fields='' islink='' isreco='' ishot='' istop='' iscomment='' limit='' orderby='' status='' iscache="1" isthumb="1"}

{$art.id} {$art.cat_catname} {$art.title}<br>

{/dc:article}
```


**参数说明：**

name：循环体的数组名，可设置为任意英文字母组合。设置后要与下方对应。

limit：显示多少条文章,默认是：0,5 （表示从第一个起，共显示5条）。

catid：文章分类ID，多个中间用,分隔。留空或不设置时获取全部文章。

fields：字段名，设置后只获取对应文章字段的值。举例：id,title,thumb,url

islink：是否仅获取外部链接的文章，1=外部链接，0=内部文章，设置为-1或留空时获取所有文章。

isreco：是否仅获取推荐的文章，1=推荐，0=不推荐，设置为-1或留空时获取所有文章。

ishot：是否仅获取热点的文章，1=热点，0=不热点，设置为-1或留空时获取所有文章。

istop：是否仅获取置顶的文章，1=置顶，0=不置顶，设置为-1或留空时获取所有文章。

iscomment：是否仅获取允许评论的文章，1=允许评论，0=不允许评论，设置为-1或留空时获取所有文章。

isthumb：是否仅获取带缩略图的文章，1=带缩略图，设置为0或留空时默认获取所有文章。

status：获取某种文章状态的文章：0无效，1正在审核，2退稿，9通过审核。不设置时，仅显示通过审核的文章。

orderby：排序方式，默认为：inputtime desc,id desc（按最新的发布时间排序）。也可以设置为：hits desc（按最多点击数排序）, listorder desc（按后台设置的排序值来排序）等。

fields：查询的字段，格式：id,title。不设置时默认为获取所有字段值。

iscache：是否启用缓存。不设置时，默认为启用，1=启用。设置0或其他值时为不启用。强烈建议启用，可以加速访问。


**（2）分类标签。**

可以获取任意分类的数据。

**举例：**


```
{dc:category name='cat' fields='' type='' modelid='' parentid='0' child='' ismenu='' limit='0,5' orderby='' iscache="1"}

{$cat.catid} {$cat.catname}<br>

{/dc:category}
```


**参数说明：**

name：循环体的数组名，可设置为任意英文字母组合。设置后要与下方对应。

limit：显示多少个分类,默认是：0,5 （表示从第一个起，共显示5条）。

parentid：父分类ID，多个中间用,分隔，表示仅获取该父分类下的所有分类。设置为0或留空时仅获取顶级分类。

type：分类类型，0=内部栏目，1=单网页，2=外部链接。设置为-1或留空时获取所有。

modelid：模型ID，0=系统，1=文章，2=下载，3=图片，二次开发时可自定义。设置为-1或留空时获取所有。

child：是否仅获取存在子栏目的分类，1=存在，设置为-1或留空时获取所有。

ismenu：是否仅获取设置为菜单显示的分类，1=菜单显示，设置为-1或留空时获取所有。

orderby：排序方式，不设置则默认为：listorder asc,catid asc。

fields：查询的字段，格式：catid,catname。不设置时默认为获取所有字段值。

iscache：是否启用缓存。不设置时，默认为启用，1=启用。设置0或其他值时为不启用。强烈建议启用，可以加速访问。


**（3）万能标签。**

可以获取任意数据表的任意数据。

**举例：**


```
{dc:get name='list' fields='' datatable='member' where='' limit='' orderby='' iscache="1"}

{$list.userid} {$list.username}<br>

{/dc:get}
```


**参数说明：**

name：循环体的数组名，可设置为任意英文字母组合。设置后要与下方对应。

datatable：要查询的数据表，必选项，不要包含数据表前缀。如：article。

where：查询条件，格式如：catid=1 and thumb!=""

limit：显示多少条数据,默认是：0,5 （表示从第一个起，共显示5条）。

orderby：排序方式，可不设置。格式如：字段名 desc,字段名 asc。

fields：查询的字段，格式如：id,title。不设置时默认为获取所有字段值。

iscache：是否启用缓存。不设置时，默认为启用，1=启用。设置0或其他值时为不启用。强烈建议启用，可以加速访问。


**（4）碎片标签。**

**举例：**

`{dc:block marker='top_tips' is_strip="" allow_html="" iscache="1"}`

**参数说明：**

marker：要获取碎片的标识符，此处设置必须跟您在后台设置的标识符值一致，如：top_tips。

is_strip：是否过滤HTML标记，1=过滤，不设置或设置其他值时为不过滤。

allow_html：当设置为过滤HTML标记时，是否保留HTML标记，为空则全部过滤，不为空则填写要保留的具体HTML标记，如<br>。

is_decode：HTML实体是否转换为字符，1=转换，不设置或设置其他值时为不转换。比如想转换&amp;为&时，可设置为1。


**（5）其他标签。**

`{$web_config.值}`

举例：

{$web_config.site_name}   获取网站名称。

此外，可以获取网站配置的任意值，可以获取的值有：site_name（网站名称），site_url（网址），index_title（首页标题），index_keywords（首页关键词），index_description（META描述），site_footer（网站底部信息），等。


更多标签使用方法，可具体参考：Src\app\index\view\cz_blue里的模板。


### 2、本系统基于caozha-admin开发，二次开发可参考此手册

码云Wiki：[https://gitee.com/dengzhenhua/caozha-admin/wikis](https://gitee.com/dengzhenhua/caozha-admin/wikis)

GitHub Wiki：[https://github.com/dengcao/caozha-admin/wiki](https://github.com/dengcao/caozha-admin/wiki)


### 3、提供了两套不同的网站风格

1、\Database\dc_article_blue.sql    蓝色风格的数据库。

2、\Database\dc_article_green.sql    绿色风格的数据库。

分别对应两种网站风格的初始化数据库，导入其中一种就可以了。

在网站后台 -》系统设置，可以设置不同的风格，也可以自己设计更多的风格。


## 更新方法

**1.0.0升级到1.2.0的方法：**

1、新增dc_art_block数据表，从1.2.0版源码/Database/目录的sql文件里可以提取。

2、将1.2.0版/Src/目录的源文件覆盖旧版本，注意修改数据库配置，还有清空缓存。


## 更新说明

**版本1.0.0，主要更新内容：**

初始版本，基于caozha-admin 1.7.2开发。

**版本1.2.0，主要更新内容：**

1、新增了碎片功能。

2、将caozha-admin后台框架版本从1.7.2升级1.8.2（ThinkPHP版本从6.0.2升级6.0.12LTS，layui版本升级至2.6.8，支持PHP8.0）。

3、修复了若干BUG。

**版本1.2.1，主要更新：**

1、支持php8.1，修复了php8.1时验证码错误等BUG。

2、更新了ThinkPHP框架到最新版。

**版本1.3.0，主要更新：**

1、新增了1套绿色风格模板，目前共有2套风格可选择。

2、修复了Ueditor一个小BUG。

**版本1.4.0，主要更新：**

1、为了系统安全，更新TP框架到6.0系列的最新版本：ThinkPHP6.0.15。

2、修改了后台若干错误链接。

**版本1.4.1，主要更新：**

更新一个css文件，修复当手机访问时不显示“提交”按钮的问题。


## 特别鸣谢

dc-article使用了以下开源代码：

caozha-admin、ThinkPHP、layui、layuimini、font-awesome、phpoffice、phpMailer等

特别致谢！

## 赞助支持：

支持本程序，请到Gitee和GitHub给我们点Star！

Gitee：https://gitee.com/dengzhenhua/dc-article

GitHub：https://github.com/dengcao/dc-article

### 关于

开发：[邓草博客 blog.5300.cn](http://blog.5300.cn)

赞助：[品络互联 www.pinluo.com](http://www.pinluo.com)  &ensp;  [AI工具箱 5300.cn](http://5300.cn)  &ensp;  [汉语言文学网 hyywx.com](http://hyywx.com)  &ensp;  [雄马 xiongma.cn](http://xiongma.cn) &ensp;  [优惠券 tm.gs](http://tm.gs)


## 界面预览


**前台页面：**


演示网址：http://5300.cn/myblog


**蓝色风格：**

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011655_29356782_7397417.png "1.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011705_12f1eaa4_7397417.png "2.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011715_50ad0b24_7397417.png "3.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011730_a4a1b6ad_7397417.png "4.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011742_d50ee753_7397417.png "5.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011751_20608321_7397417.png "6.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011802_0baf6cca_7397417.png "7.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011812_b0b5927a_7397417.png "8.png")



**绿色风格：**


![输入图片说明](https://images.gitee.com/uploads/images/2022/0401/030131_0ecc1017_7397417.jpeg "green.jpg")




**后台管理功能页面：**


![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/095751_8a313232_7397417.png "11.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/004200_03967767_7397417.jpeg "12.jpg")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/004219_bddd7960_7397417.jpeg "13.jpg")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/004228_77a586e1_7397417.jpeg "14.jpg")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/004237_802810be_7397417.jpeg "15.jpg")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0602/181454_9d357745_7397417.png "10.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/101336_1575442d_7397417.png "10-1.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0604/110154_cb9f5759_7397417.png "10-2.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0531/221652_31b46dd9_7397417.png "8.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0531/221706_b4fc6e99_7397417.png "9.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111034_0fcc6524_7397417.png "1.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111043_e0a9482f_7397417.png "2.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111051_b6abdc55_7397417.png "3.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111132_8860fb7d_7397417.png "4.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111139_8230a7f8_7397417.png "5.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111151_7aaf6aa7_7397417.png "6.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0522/111159_fb128fff_7397417.png "7.png")


**文章评论（PC端）：**

![输入图片说明](https://images.gitee.com/uploads/images/2020/0611/145140_3e613b5d_7397417.png "16.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0611/135914_73eb0310_7397417.png "19.png")

**文章评论（手机端）：**

![输入图片说明](https://images.gitee.com/uploads/images/2020/0612/152711_77208177_7397417.jpeg "5.jpg")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0612/152720_633821db_7397417.jpeg "6.jpg")


**评论后台：**

![输入图片说明](https://images.gitee.com/uploads/images/2020/0611/124804_b09945aa_7397417.png "17.png")

![输入图片说明](https://images.gitee.com/uploads/images/2020/0611/135927_03fa9d83_7397417.png "20.png")

**评论可设置项：**

![输入图片说明](https://images.gitee.com/uploads/images/2020/0612/174208_53940656_7397417.png "18.png")
