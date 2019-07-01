<?php
router()
	->error(405,'show_error')
	->error(406,'show_error')
    ->get('pic','pic/:name','api/file/pic','') //文件
	->get('article','article/:id:d.html','portal/post/article','.html') //文章
    ->get('article_list','alist/:slug','portal/post/article_list') //文章分类
    ->get('tag','topic/:slug','portal/post/tag')//标签
    ->get('tag_all','topic-all/','portal/post/tag_all')
    ->get('goods','goods/:id:d.html','portal/post/goods','.html') //商品商品
    ->get('goods_list','goods_list/:slug','portal/post/goods_list') //商品分类
	->run();