# -*- coding:utf-8 -*-

#### LabelAttr

#1st edition aimed at labeling attributes of cars
1st editon confirmed on July 11th 2018 by Xiaohui Xu and Gang Chen
Attribute in this version:
1-Car color, 12 candidates:
2-Car direction, 8 candidates:
3-Car type, 8 candidates:
4-Car plate type, 7 candidates:

使用介绍：

服务器配置：
1,安装apache2: sudo apt-get install apache2,安装完成后后，服务器文件入口在 /var/www/html 文件夹下。在此路径下新建welcome.php文件，输出php系统配置，方便检查。welcome.php内 echo phpinfo()。注意开启mysqli和pdo，方法为：在/etc/php/7.0/cli/php.ini文件中，将第890和894行 extension=php_mysqli.dll和extension=php_pdo_mysql.dll取消注释；
安装mysql:sudo apt-get install mysql-server; sudo apt-get install mysql-clinet; sudo apt-get install libmysqlcleint-dev。然后mysql -u root -p进入数据库，建立labelattr数据库。
2,图片数据复制：在/var/www/html文件夹下建立软连接至图片数据库，比如图片路径为/home/xiaohui/Dataset，则在/var/www/html下运行:ls -s /home/xiaohui/Dataset/Datasets Dataset。实际使用时将/home/xiaohui/Dataset/Datasets换成实际图片文件所在位置。
3,代码复制：将labelattr文件夹拷至/var/www/html文件夹内。第一步：在/var/www/html/labelattr下进入mysql数据库，source运行labelattr.sql，建立相应的三个表。三个表分别为users,user_label,img_label。users记录操作者的账户密码，user_label记录针对每个图片的每个属性生成一条记录，img_label针对每个图片生成一行。每行包含所有属性。 第二步：python运行gen_list.py文件，将会在/var/www/html下生成dataset.txt文件，其内部为所有需要标注的img的list。第三步，python运行add2sql.py，将dataset.txt中的img_list导入数据库，生成未标注的空值数据表，等待填充标注，目前的代码为根据标注用户随机分配任务。


用户前端使用：
浏览器中链接:服务器ip地址/labelattr，正确输入账户名以及密码，选择标注的属性即可登录进行标注。


2018-7-26 Version2:
1,更改服务器将数据提交至mysql的方式，原来为对于每一条数据都push到user_label和img_label表中，现在为先收集attr值为0和1的imgid集合，然后直接一次更新掉这些imgid的属性值，大大提高了速度。
Todo:数据结构优化，自动根据可能的attr值，建立多个array收集imgid。

2018-7-31 Version3:
1,更改服务器数据提交至mysql方式，此版本为正式稳定版，相比与上一版，适应性更强，采用mysql的拼接语句方式update table set item=CASE Id when 1 then 'value' when 2 then 'value' ... end where Id in (1, 2, ...)的方式;
2,目前form提交的_POST数组中，固定三种 draw_list0001, text_box0001, radio0001，根据每个图片的需求选择性注释掉labelattr.php中的对应部分，即可不产生相应的变量组。
