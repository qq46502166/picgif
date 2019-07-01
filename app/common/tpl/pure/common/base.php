<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="Cache-Control" content="no-transform">
	<meta http-equiv="Cache-Control" content="no-siteapp">
	<meta name="applicable-device" content="pc,mobile">
	<title><?=$title?>_后台管理</title>
	<link href="/static/admin/pure/css/pure.css?v=1.01" rel="stylesheet" type="text/css">
	<link href="/static/admin/pure/css/style.css?v=1.03" rel="stylesheet" type="text/css">
	<link href="//at.alicdn.com/t/font_723404_m5nokmitlpk.css" rel="stylesheet">
	{%block@head_remark%}
</head>
<body>
<header id="header">
	<div class="nav">
		<a class="" href="<?=url('/')?>" target="_blank">打开前台</a>
		<a class="" href="<?=url('admin/login/logout')?>">退出登陆</a>
	</div>
</header>
<aside id="aside">
	<div class="box">
		<div class="title">控制台</div>
		<script charset="UTF-8" type="text/javascript">
			//var router="<?//=$router['module']?>/<?//=strtolower($router['ctrl'])?>/<?//=$router['action']?>";
			var router={module:"<?=$router['module']?>",ctrl:"<?=strtolower($router['ctrl'])?>",action:"<?=$router['action']?>"};
		</script>
		<div class="menu">
			<div class="dh-menu">
				<ul class="dh-menu-list">
					<li class="dh-menu-item dh-menu-has-children" router="admin/option"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wenzhangguanli"></i>网站设置</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/option/all"><a href="<?=url('admin/option/all')?>" class="dh-menu-link">全局设置</a></li>
							<li class="dh-menu-item" router="admin/option/citys"><a href="<?=url('admin/option/citys')?>" class="dh-menu-link">区域设置</a></li>
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/portal"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wenzhangguanli"></i>门户管理</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item dh-menu-has-children" router="admin/portal/post1">
								<a href="javascript:;" class="dh-menu-link">文章内容</a>
								<ul class="dh-menu-children" id="admin-portal-1">
									<li class="dh-menu-item" id="admin-portal-category-1"><a href="<?=url('admin/portal/category')?>?type=1" class="dh-menu-link">分类管理</a></li>
									<li class="dh-menu-item" id="admin-portal-category_add-1"><a href="<?=url('admin/portal/category_add')?>?type=1" class="dh-menu-link">添加分类</a></li>
									<li class="dh-menu-item" id="admin-portal-post-1"><a href="<?=url('admin/portal/post')?>?type=1" class="dh-menu-link">文章管理</a></li>
									<li class="dh-menu-item" id="admin-portal-post_add-1"><a href="<?=url('admin/portal/post_add')?>?type=1" class="dh-menu-link">添加文章</a></li>
								</ul>
							</li>
							<li class="dh-menu-item dh-menu-has-children" router="admin/portal/post4">
								<a href="javascript:;" class="dh-menu-link">虚拟商品</a>
								<ul class="dh-menu-children" id="admin-portal-4">
									<li class="dh-menu-item" id="admin-portal-category-4"><a href="<?=url('admin/portal/category')?>?type=4" class="dh-menu-link">分类管理</a></li>
									<li class="dh-menu-item" id="admin-portal-category_add-4"><a href="<?=url('admin/portal/category_add')?>?type=4" class="dh-menu-link">添加分类</a></li>
									<li class="dh-menu-item" id="admin-portal-post-4"><a href="<?=url('admin/portal/post')?>?type=4" class="dh-menu-link">虚拟商品管理</a></li>
									<li class="dh-menu-item" id="admin-portal-post_add-4"><a href="<?=url('admin/portal/post_add')?>?type=4" class="dh-menu-link">添加虚拟商品</a></li>
								</ul>
							</li>

							<!--li class="dh-menu-item dh-menu-has-children" router="admin/portal/comment"><a href="#" class="dh-menu-link">评论管理</a>
								<ul class="dh-menu-children">
									<li class="dh-menu-item"><a href="#" class="dh-menu-link">评论管理</a></li>
								</ul>
							</li-->
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/bbs"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wenzhangguanli"></i>论坛管理</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/bbs/category"><a href="<?=url('admin/bbs/category')?>" class="dh-menu-link">分类管理</a></li>
							<li class="dh-menu-item" router="admin/bbs/post"><a href="<?=url('admin/portal/post')?>" class="dh-menu-link">文章管理</a></li>
							<li class="dh-menu-item dh-menu-has-children" router="admin/portal/comment"><a href="#" class="dh-menu-link">评论管理</a>
								<ul class="dh-menu-children">
									<li class="dh-menu-item"><a href="#" class="dh-menu-link">评论管理</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/weixinqun"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wechat"></i>微信群管理</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/weixinqun/category"><a href="<?=url('admin/weixinqun/category')?>" class="dh-menu-link">分类管理</a></li>
							<li class="dh-menu-item" router="admin/weixinqun/qun"><a href="<?=url('admin/weixinqun/qun')?>" class="dh-menu-link">微信群</a></li>
							<li class="dh-menu-item" router="admin/weixinqun/gongzhonghao"><a href="<?=url('admin/weixinqun/gongzhonghao')?>" class="dh-menu-link">公众号</a></li>
							<li class="dh-menu-item" router="admin/weixinqun/gongzhonghao"><a href="<?=url('admin/weixinqun/gerenweixin')?>" class="dh-menu-link">个人微信</a></li>
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/wechat"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wechat"></i>微信管理</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/wechat/menu"><a href="<?=url('admin/wechat/menu')?>" class="dh-menu-link">菜单管理</a></li>
							<li class="dh-menu-item" router="admin/wechat/user"><a href="<?=url('admin/portal/category')?>" class="dh-menu-link">用户管理</a></li>
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/caiji"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wenzhangguanli"></i>采集设置</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/caiji/handler"><a href="<?=url('admin/caiji/handler')?>" class="dh-menu-link">项目管理</a></li>
							<li class="dh-menu-item" router="admin/caiji/queue"><a href="<?=url('admin/caiji/queue')?>" class="dh-menu-link">采集队列</a></li>
							<li class="dh-menu-item" router="admin/caiji/setting"><a href="<?=url('admin/caiji/setting')?>" class="dh-menu-link">模板</a></li>
						</ul>
					</li>
					<li class="dh-menu-item dh-menu-has-children" router="admin/other"><a href="javascript:;" class="dh-menu-link"><i class="iconfont icon-wenzhangguanli"></i>其他设置</a>
						<ul class="dh-menu-children">
							<li class="dh-menu-item" router="admin/other/queue"><a href="<?=url('admin/other/queue')?>" class="dh-menu-link">定时任务</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<br><br><br><br><br>
		</div>
	</div>
</aside>
<main id="main">
{%block@main%}
	<div class="footer">&copy; <?=date('Y')?>  dahulu.cc</div>
</main>
{%include@common/js%}
{%block@javascript%}
<script type="text/javascript">
	$(document).ready(function () {
		//侧栏菜单
		asideMenu();
	});
</script>
</body>
</html>