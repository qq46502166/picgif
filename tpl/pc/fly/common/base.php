<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="applicable-device" content="pc,mobile">
    {%block@title%}
    <link rel="stylesheet" href="/static/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/static/fly/css/global.css">
    <link href="/static/favicon.ico?v=1.1" rel="shortcut icon" type="image/x-icon">
</head>
<body>
{%include@common/header%}
<!--导航-->
<div class="fly-panel fly-column">
    <div class="layui-container">
        <ul class="layui-clear">
            <li class="layui-hide-xs"><a href="/">首页</a></li>
            <li class="layui-this"><a href="">提问</a></li>
            <li><a href="">分享<span class="layui-badge-dot"></span></a></li>
            <li><a href="">讨论</a></li>
            <li><a href="">建议</a></li>
            <li><a href="">公告</a></li>
            <li><a href="">动态</a></li>
            <li class="layui-hide-xs layui-hide-sm layui-show-md-inline-block"><span class="fly-mid"></span></li>
            <?php if($isLogin):?>
            <!-- 用户登入后显示 -->
            <li class="layui-hide-xs layui-hide-sm layui-show-md-inline-block"><a href="#/user/index.html">我发表的贴</a></li>
            <li class="layui-hide-xs layui-hide-sm layui-show-md-inline-block"><a href="#/user/index.html#collection">我收藏的贴</a></li>
            <?php endif;?>
        </ul>

        <div class="fly-column-right layui-hide-xs">
            <span class="fly-search"><i class="layui-icon"></i></span>
            <a href="#add.html" class="layui-btn">发表新帖</a>
        </div>
        <div class="layui-hide-sm layui-show-xs-block" style="margin-top: -10px; padding-bottom: 10px; text-align: center;">
            <a href="#add.html" class="layui-btn">发表新帖</a>
        </div>
    </div>
</div>
<!--//end 导航-->
{%content%}
<div class="fly-footer">
    <p> &copy;  2016~<?=date('Y')?> <a href="<?=$site_url?>/"><?=$site_name?></a>版权所有</p>
</div>
<!--公共js-->
<script src="/static/lib/layui/layui.js"></script>
<!--公共js-->
{%block@javascript%}

<script>
    layui.cache.page = 'jie';
    layui.cache.user = {
        username: '游客'
        ,uid: -1
        ,avatar: '/static/fly/images/avatar/00.jpg'
        ,experience: 83
        ,sex: '男'
    };
    layui.config({
        version: "3.0.0",
        base: '/static/fly/mods/'
    }).extend({
        fly: 'index'
    }).use('fly');
</script>

</body>
</html>