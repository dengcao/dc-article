# dc-article 文章内容管理系统 1.0.0

dc-article是一个通用的文章内容管理系统，基于开源的caozha-admin开发，采用前后端分离的模板和标签化方式，支持文章内容管理、栏目分类管理、评论管理、友情链接管理、远程图片获取器等功能。可以使用本系统很轻松地架构新闻类网站、文章类网站、图片展示类网站或个人博客网站。系统特点：易上手，零门槛，拿来即用，界面清爽极简，极便于二次开发。

### 系统功能

1、系统设置

2、管理员管理

3、权限组管理

4、系统日志

5、后台功能地图

6、文章内容管理

7、栏目分类管理（支持无限级别分类）

8、评论管理（支持盖楼评论，支持设置屏蔽词、验证码、是否需审核等，可整合到任何场景。可以自动适配电脑、平板和手机等不同客户端。）

9、友情链接管理

10、整合了百度UEditor（编辑器）、caozha-getimg（远程图片获取器，可以获取远程经防盗的图片，绕过防盗检测）等各种常用插件，可去演示页面查看。

主要提供了以上基础功能，您可以在此基础上拓展和开发出不同的应用。

### 安装使用

**开发环境**

本人开发此系统使用的本地环境是：phpstudy8.1集成工具，phpMyAdmin 4.8.5，Apache2.4.39（或Nginx1.15.11），PHP7.3.4，MySQL5.7.26。

事实上，您不需要使用跟以上完全一致的环境也可以正常运行本系统，理论上只要PHP>=7.1且PHP<7.4即可。如有不兼容，建议模拟本环境测试，并欢迎您提建议和反馈BUG。

注意：暂不支持PHP8.0以上版本。


**快速安装**

1、PHP版本必须使用7.1、7.2或7.3，推荐使用：PHP7.3。

2、上传目录/Src/内所有源码到服务器，并设置网站的根目录指向运行目录/public/。（此为ThinkPHP6.0的要求）

3、将/Database/目录里的.sql文件导入到MYSQL数据库。

4、修改文件/config/database.php，配置您的数据库信息（如果测试时启用了/.env，还需要修改文件/.env，系统会优先使用此配置文件）。

5、后台访问地址：http://您的域名/admin.php   (账号：caozha   密码：123456)


**伪静态设置**

1、ThinkPHP框架必须在运行目录下设置伪静态才能正常访问，否则会显示404错误。

2、如果您使用的是Apache，伪静态设置为（.htaccess）：

<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

RewriteRule ^getimg/(.*) get_img/index\.php\?url=$1

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
#RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
</IfModule>


3、如果您使用的是Nginx，以wdCP和宝塔Linux面板为例，伪静态设置为：

    index index.php;
    rewrite ^/getimg/(.*)$ /get_img/index.php?url=$1  last;
    if (!-e $request_filename) {
       rewrite  ^(.*)$  /index.php?s=/$1  last;
       break;
    }
    


4、在网站运行目录（/public/）下，有两个文件：.htaccess和nginx.htaccess，分别是Apache和Nginx的伪静态文件，您可以直接拿来使用。


**远程图片获取器 使用说明**

caozha-getimg是一款图片获取器，可以获取远程经防盗的图片，绕过防盗检测。

直接调用方式：
http://（域名）/getimg/https（或http，根据远程图片决定）/(远程图片URL，即除https://部分的URL)

参考实例：http://www.域名.com/getimg/https/www.baidu.com/img/PCtm_d9c8750bed0b3c7d089fa7d55720d6cf.png


**开发手册**

1、模板制作，参考：Src\app\index\view\cz_blue。

2、本系统基于caozha-admin开发，二次开发可参考此手册。

码云Wiki：[https://gitee.com/caozha/caozha-admin/wikis](https://gitee.com/caozha/caozha-admin/wikis)

GitHub Wiki：[https://github.com/cao-zha/caozha-admin/wiki](https://github.com/cao-zha/caozha-admin/wiki)


### 特别鸣谢

dc-article使用了以下开源代码：

caozha-admin、ThinkPHP、layui、layuimini、font-awesome等

特别致谢！

### 赞助支持：

支持本程序，请到Gitee和GitHub给我们点Star！

Gitee：https://gitee.com/caozha/dc-article

GitHub：https://github.com/cao-zha/dc-article

### 关于开发者

开发：邓草 www.caozha.com

鸣谢：品络 www.pinluo.com  &ensp;  穷店 www.qiongdian.com


### 界面预览


**前台页面：**


演示网址：http://caozha.com


![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011655_29356782_7397417.png "1.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011705_12f1eaa4_7397417.png "2.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011715_50ad0b24_7397417.png "3.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011730_a4a1b6ad_7397417.png "4.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011742_d50ee753_7397417.png "5.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011751_20608321_7397417.png "6.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011802_0baf6cca_7397417.png "7.png")

![输入图片说明](https://images.gitee.com/uploads/images/2022/0322/011812_b0b5927a_7397417.png "8.png")


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