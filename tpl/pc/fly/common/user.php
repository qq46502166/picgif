{%content@common/base-portal%}
<div class="layui-container fly-marginTop fly-user-main">

    <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="user">
        <li class="layui-nav-item" style="line-height: 25px;padding-left:20px;>
           <span style="display:block"> <img src="/<?=(\core\Session::get('user.avatar')?:'uploads/user/default.png')?>"></span>
            <span style="display: block">用户名：share98</span>
            <span style="display: block">余  额：100</span>
        </li>
       <li class="layui-nav-item">
           <a href="javascript:;"  id="portal-user"><i class="layui-icon">&#xe612;</i>个人中心</a>
           <dl class="layui-nav-child"> <!-- 二级菜单 -->
               <dd><a href="<?=url('portal/user/index')?>"  id="portal-user-index">我的资料</a></dd>
               <dd><a href="">我的余额</a></dd>
               <dd><a href="">充值记录</a></dd>
           </dl>
       </li>
        <li class="layui-nav-item">
            <a href="javascript:;"  id="portal-user"><i class="layui-icon">&#xe698;</i>购物中心</a>
            <dl class="layui-nav-child"> <!-- 二级菜单 -->
                <dd><a href="<?=url('portal/user/index')?>"  id="portal-user-index">我的购物车</a></dd>
                <dd><a href="">我的订单</a></dd>
            </dl>
        </li>

        <li class="layui-nav-item">
            <a href="set.html">
                <i class="layui-icon">&#xe620;</i>
                基本设置
            </a>
        </li>
        <li class="layui-nav-item">
            <a href="message.html">
                <i class="layui-icon">&#xe611;</i>
                我的消息
            </a>
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
