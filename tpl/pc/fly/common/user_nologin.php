{%content@common/base-portal%}
<div class="layui-container fly-marginTop fly-user-main">

    <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="user">
        <li class="layui-nav-item" style="line-height: 25px;padding-left:20px;>
           <span style="display:block"> <img src="/<?=(\core\Session::get('user.avatar')?:'uploads/user/default.png')?>"></span>
            <span style="display: block">用户名：游客</span>
        </li>
        <li class="layui-nav-item">
            <a href="<?=url('portal/shop/cart')?>"  id="portal-user"><i class="layui-icon">&#xe698;</i>我的购物车</a>

        </li>

        <li class="layui-nav-item">
            <a href="<?=url('portal/shop/myorder')?>"  id="portal-user"><i class="layui-icon">&#xe60a;</i>订单查询</a>
        </li>
    </ul>

    <div class="site-tree-mobile layui-hide">
        <i class="layui-icon">&#xe602;</i>
    </div>
    <div class="site-mobile-shade"></div>

    <div class="site-tree-mobile layui-hide">
        <i class="layui-icon">&#xe602;</i>
    </div>
    <div class="site-mobile-shade"></div>
    <script type="text/javascript">
        var navCurrent=document.getElementById("<?=$router['module']?>-<?=strtolower($router['ctrl'])?>");
        if(navCurrent){
            navCurrent.classList.add("layui-this");
            navCurrent.parentNode.classList.add("layui-nav-itemed");
        }
    </script>
    {%block@article%}
</div>
