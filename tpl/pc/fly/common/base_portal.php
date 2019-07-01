<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    {%block@title%}
    <link rel="stylesheet" href="/static/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/static/fly/css/global.css?v=1.1">
    <link href="/static/favicon.ico?v=1.2" rel="shortcut icon" type="image/x-icon">
</head>
<body>
{%include@common/header%}
<!--导航-->
<div style="margin-top: 44px"></div>
<!--//end 导航-->
{%block@article%}
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

    var tip_box_a_h = document.querySelectorAll('.tip_box a')[0].offsetHeight;
    var i=0;
    setInterval(function () {
        var startScroll=setInterval(function () {
            i++;
            document.querySelector('.tip_box').style.bottom = (tip_box_a_h/10)*i+'px';
            if(i%10===0)clearInterval(startScroll);
        },50);
        if(i>(10*(document.querySelectorAll('.tip_box a').length-2)))
            i = 0;
    },4500);
</script>
<!--公共js-->
{%block@javascript%}
</body>
</html>