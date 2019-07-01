<div class="fly-header layui-bg-black">
    <div class="layui-container" style="position: relative;height: 60px">
        <a class="fly-logo" href="/" title="<?=$site_name?>">
            <img src="/static/fly/images/logo.png" alt="<?=$site_name?>" title="返回首页">
        </a>
        <div class="search-box layui-hide-xs">
            <input class="search-box-input" placeholder="搜片、搜剧"><span class="search-box-btn"><i class="layui-icon layui-icon-search"></i></span>
            <span class="search-key">
                <span>热门&nbsp;:</span>
                <a href="" class="hotwords">主播</a>
                <a href="" class="hotwords">多颂</a>
                <a href="" class="hotwords">韩宝贝</a>
                <a href="" class="hotwords">青草</a>
                <a href="" class="hotwords">敏晶</a>
            </span>
        </div>

        <ul class="layui-nav fly-nav-user">
            <?php if($isLogin):?>
                <!-- 登入后的状态 -->
                <li class="layui-nav-item">
                    <a class="fly-nav-avatar" href="javascript:;">
                        <cite class="layui-hide-xs"><?=\core\Session::get('username')?></cite>
                        <i class="iconfont icon-renzheng layui-hide-xs"></i>
                        <i class="layui-badge fly-badge-vip layui-hide-xs">VIP3</i>
                        <img src="<?=(\core\Session::get('user.avatar')?:'/uploads/user/default.png')?>">
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="<?=url('portal/user/info')?>"><i class="layui-icon layui-icon-set-sm"></i>个人设置</a></dd>
                        <dd><a href="<?=url('portal/user/message')?>"><i class="iconfont icon-tongzhi" style="top: 4px;"></i>我的消息</a></dd>
                        <dd><a href="<?=url('portal/user/myorder')?>"><i class="layui-icon layui-icon-home" style="margin-left: 2px; font-size: 22px;"></i>我的订单</a></dd>
                        <hr style="margin: 5px 0;">
                        <dd><a href="<?=url('portal/index/logout')?>" style="text-align: center;">退出</a></dd>
                    </dl>
                </li>
            <?php else:?>
                <!-- 未登入的状态 -->
                <li class="layui-nav-item">
                    <a class="iconfont icon-touxiang layui-hide-xs" href="<?=url('portal/index/login')?>"></a>
                </li>
                <li class="layui-nav-item">
                    <a href="<?=url('portal/index/login')?>">登入</a>
                </li>
                <li class="layui-nav-item">
                    <a href="<?=url('portal/index/reg')?>">注册</a>
                </li>
                <!--li class="layui-nav-item layui-hide-xs">
                    <a href="/app/qq/" onclick="layer.msg('正在通过QQ登入', {icon:16, shade: 0.1, time:0})" title="QQ登入" class="iconfont icon-qq"></a>
                </li>
                <li class="layui-nav-item layui-hide-xs">
                    <a href="/app/weibo/" onclick="layer.msg('正在通过微博登入', {icon:16, shade: 0.1, time:0})" title="微博登入" class="iconfont icon-weibo"></a>
                </li-->
            <?php endif; ?>
        </ul>
    </div>
    <div style="width: 100%;border-top: 1px solid #555;overflow: hidden;height: 40px">
        <div class="layui-container">
            <ul class="layui-nav my-nav">
                <li class="layui-nav-item"><a href="/">首页</a></li>
                <li class="layui-nav-item"><a href="<?=url('@zhubo@',['slug'=>''])?>">主播大全</a></li>
                <li class="layui-nav-item"><a href="<?=url('@article_list@',['slug'=>'xinshoujiaocheng'])?>">新手教程</a></li>
                <li class="layui-nav-item"><a href="">加入VIP</a></li>
                <li class="layui-nav-item"><a href="<?=url('@article@',['id'=>6820])?>">关于本站</a></li>
                <li class="layui-hide-xs layui-nav-item shanshuo">永久防封网址：<span>www.ciaji.my</span></li>
            </ul>
        </div>
    </div>
</div>