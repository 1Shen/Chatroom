# Chatroom

# 使用注意
0.下载xampp并配置好对应的环境变量
1.将文件夹放在xampp\htdocs\下
2.修改xampp\apache\conf\extra\httpd-vhosts.conf的相关条目
    DocumentRoot "...xampp\htdocs\Chatroom\public"
3.修改xampp\apache\conf\httpd.conf的相关条目
    引入三个模块
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule proxy_http_module modules/mod_proxy_http.so
        LoadModule proxy_wstunnel_module modules/mod_proxy_wstunnel.so
    最后面添加：
        ProxyPass /web/websocket/ ws://l:8080/web/websocket/
        ProxyPass / http://127.0.0.1:8080/
4.安装composer、laravel
5.启动xampp的Apache和Mysql服务器后登录localhost/phpmyadmin建立数据库'chatroom'，具体配置xampp及laravel不再赘述，详情查看laravel学院的入门教程
6.执行迁移文件构建数据表
7.进入Chatroom目录后，运行 php app/Http/Controllers/Dura.php
8.终端出现监听字样后浏览器输入localhost即可（浏览器应支持session和websocket） 

# 文件目录
app下为各类控制器、模型等，主要逻辑位于app\Http\Controllers\下，模型位于app\下

database\migrations下为数据库迁移文件，记录了数据表的格式及约束等

public是主文件夹，包含项目的css、js、resource、外部引用lib等，index.php为laravel逻辑，并不是本项目的主页

resources\views\chatroom是本项目的各页面文件，使用了laravel的blade模板

routes\web.php是本项目的路由文件，路由的说明见laravel学院

config\database.php以及.env文件中有部分数据库相关的设置，主要是管理员登录及对应数据库的连接

其余文件及文件夹暂为使用，具体的结构说明见laravel学院