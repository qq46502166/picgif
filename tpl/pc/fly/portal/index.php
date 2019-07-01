{%extend@common/portal%}
{%block@title%}
<title><?=$site_title?></title>
<meta name="keywords" content="<?=$site_keywords?>">
<meta name="description" content="<?=$site_description?>">
{%end%}
{%block@article%}
<div class="layui-col-md8">
    <div class="fly-panel">
        <?php if ($goods):?>
        <ul class="index-list">
            <h2>股票教程与工具</h2>
            <?php foreach ($goods as $good):?>
                <li>
                    <a class="" href="<?=url('@goods@',['id'=>$good['id']])?>"><img <?=($good['thumb'] ?'src="'.$good['thumb'].'_200x200.jpg" alt="'.$good['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                    <a href="<?=url('@goods@',['id'=>$good['id']])?>"><?=$good['title']?></a>
                </li>
            <?php endforeach;?>
        </ul>
        <?php endif;?>
        <?php if ($articles):?>
        <ul class="list-block">
            <h2 class="list-block-title">大盘分析</h2>
            <?php foreach ($articles as $article):?>
                <li class="list-block-item">
                    <a class="list-block-pic" href="<?=url('@article@',['id'=>$article['id']])?>"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                    <div class="list-block-detail">
                        <h3 class="item-title"><a href="<?=url('@article@',['id'=>$article['id']])?>" ><?=$article['title']?></a></h3>
                        <p class="item-desc layui-hide-xs"><?=($article['excerpt'] ? :\extend\Helper::text_cut($article['content'],200))?></p>
                        <p class="item-about"><a class="item-user" href="<?=url('@article_list@',['slug'=>$article['category_slug']])?>"><?=$article['category_name']?></a><span class="item-date"><?=date('Y-m-d H:i',$article['create_time'])?></span></p>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
        <?php endif;?>

    </div>
    <div class="fly-panel" style="margin-bottom: 0;">
        <?php if($bbsData):?>
        <ul class="fly-list">
            <h2 class="text-title">最新讨论</h2>
            <?php foreach ($bbsData as $item):?>
                <li>
                    <a href="javascript:;" class="fly-avatar">
                        <img src="<?=($item['avatar']? :'/uploads/user/default.png')?>" alt="<?=$item['username']?>">
                    </a>
                    <div class="fly-list-title">
                        <a href="<?=url('@bbs_list@',['id'=>$item['category_id']])?>" class="layui-badge"><?=$item['category_name']?></a>
                        <a href="<?=url('@bbs_show@',['id'=>$item['id']])?>"><?=$item['title']?></a>
                    </div>
                    <div class="fly-list-info">
                        <a href="javascript:;"><cite><?=$item['username']?></cite></a>
                        <span><?=date('Y-m-d H:i',$item['create_time'])?></span>
                        <span class="fly-list-nums">
                            <i class="iconfont icon-pinglun1" title="评论"></i> <?=$item['comments_num']?>
                        </span>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    <?php endif;?>
    </div>
</div>
{%end%}
{%block@javascript%}

<script type="text/javascript" charset="utf-8">
    layui.use(['jquery','layer','form'], function(){
        var a='8393';
        return;
        var layer = layui.layer;
        var $=layui.$;
        var form=layui.form;
        layer.ready(function(){
            form.on('select(login_type)', function(data){
                var selectText=$(data.elem).find("option:selected").text();
                $("label[for='L_type_content']").text(selectText);
                $("#L_type_content").attr("name",data.value);
            });
        });
    });
    function Code() {
        document.getElementById("img").src="<?=url('portal/index/captcha')?>?w=140&h=36&"+Math.random();
    }
</script>
{%end%}