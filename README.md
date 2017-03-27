## 简介

* 项目原理: 模拟微信网页版登录,如果网页版本没有的功能，这个项目项目也是无能为力的
* 该项目主要目的想利用swoole，增强原生PHP cli的能力
* 直接使用hanson/vbot库，swoole-bot只是增加swoole版本的入口,这样有利用新功能迭代
* 已经支持多用户登录，修改Proccess.php 的$workNum可以控制默认启动进程数
* 自动监控子进程，意外退出后会自动恢复，保证服务稳定性
* 目前没考虑修改原作者的底层封装，后期可考虑用swoole task提高性能

## 安装

### 环境要求

* PHP >= 7.0
* swoole >= 1.8.9

### 安装


1. composer

```
composer require kcloze/swoole-bot
```

2. git

```
git clone https://github.com/kcloze/swoole-bot.git
cd swoole-bot
composer install
```

然后执行

``` 
chmod u+x server.sh
./server.sh start|stop|restart

``` 
3. 配置nginx访问,根目录为:swoole-bot/log/session，必须添加autoindex选项，qr.png为扫描登录的二维码
```
        root   /data/www/swoole-bot/log/session;
        index  index.html index.htm index.php;
        autoindex on;

```
* 浏览器访问：localhost,在点击随机生成的目录下的图片（注意选择日期最新的目录，每次过期会重新生成session目录）
* 手机扫码登录



### 体验demo
* 微信添加微信好友：ysrg2014
* 输入验证关键字：666
* 对话输入自己想说的话，微信机器人机会跟您聊天了

![效果截图1](demo-1.png)


## 文档

[详细文档](https://github.com/HanSon/vbot/wiki)




## 参考项目

[hanson/vbot](https://github.com/HanSon/vbot)


## 感谢

[hanson/vbot](https://github.com/HanSon/vbot)

## QQ群
* 141059677
