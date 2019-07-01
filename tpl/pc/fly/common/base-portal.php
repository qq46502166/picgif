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
            <li><a href="/">通知</a></li>
        </ul>
        <div class="fly-column-right">
            <span class="fly-search"><i class="layui-icon"></i></span>
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
<script type="text/javascript" charset="utf-8">
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
<!--公共js-->
{%block@javascript%}
</body>
</html>