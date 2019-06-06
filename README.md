# Chatroom

# 使用注意
# 1.将文件夹放在xampp\htdocs\下
# 2.修改xampp\apache\conf\extra\httpd-vhosts.conf的相关条目
DocumentRoot "...xampp\htdocs\Chatroom\public"
# 3.修改xampp\apache\conf\httpd.conf的相关条目
引入三个模块
LoadModule proxy_module modules/mod_proxy.so
LoadModule proxy_http_module modules/mod_proxy_http.so
LoadModule proxy_wstunnel_module modules/mod_proxy_wstunnel.so
最后面添加：
ProxyPass /web/websocket/ ws://l:8080/web/websocket/
ProxyPass / http://127.0.0.1:8080/

# 4.进入Chatroom目录后，运行 php app\sock.php
# 5.终端出现监听字样后浏览器输入localhost即可（浏览器应支持session和websocket） 