{%extend@common/portal%}
{%block@title%}
<title><?=($category['seo_title']?:$category['name']);?>_<?=$site_name?></title>
<meta name="keywords" content="<?=$category['seo_keywords']?>">
<meta name="description" content="<?=$category['seo_description']?>">
{%end%}
{%block@article%}
<div class="layui-col-md8">
    <div class="fly-panel">
        <ul class="list-block">
            <h2 class="list-block-title"><?=$title?></h2>
            <?php if($data): foreach ($data as $article):?>
                <li class="list-block-item">
                    <a class="list-block-pic" href="<?=url('@article@',['id'=>$article['id']])?>"><img <?=($article['thumb'] ?'src="'.$article['thumb'].'_200x200.jpg" alt="'.$article['title'].'"' : 'src="/uploads/images/no.gif" alt="没有缩略图"')?> class="item-img"></a>
                    <div class="list-block-detail">
                        <h3 class="item-title"><a href="<?=url('@article@',['id'=>$article['id']])?>" ><?=$article['title']?></a></h3>
                        <p class="item-desc layui-hide-xs"><?=($article['excerpt'] ? :\extend\Helper::text_cut($article['content'],200))?></p>
                        <p class="item-about"><a class="item-user" href="<?=url('@article_list@',['slug'=>$article['category_slug']])?>"><?=$article['category_name']?></a><span class="item-date"><?=date('Y-m-d H:i',$article['create_time'])?></span></p>
                    </div>
                </li>
            <?php endforeach;endif;?>
        </ul>
        <?=$page?>
    </div>

</div>
{%end%}

