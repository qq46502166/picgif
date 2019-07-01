{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> &gt; 门户管理 &gt; <?=$pindao[$type]?>频道 &gt; <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-item" id="jtab_inline">
				
					<div class="tab-hander">
						<a href="javascript:;" class="tab-hander-item tab-select" data="tab-a">基本</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-b">聚合</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-c">网盘</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-d">扩展</a>
						<a href="javascript:;" class="tab-hander-item" data="tab-e">其他</a>
					</div>
					<form class="pure-form pure-form-stacked mb-8" action="" method="post" id="myform">
						 <fieldset>
							<div class="tab-item" id="tab-a">
								<table class="full pure-table pure-table-bordered">
									<tr>
										<td width="70"><label for="title">选项 <span class="red">*</span></label></td>
										<td>
											权限:
											<select id="permissions" class="pure-select dh-inline" name="permissions">
												<?php foreach ($allow as $i =>$item):?>
													<option value="<?=$i?>"<?=echo_select($data['permissions'],$i)?>><?=$item?></option>
												<?php endforeach;?>
											</select>
											分类:
											<select id="category_id" class="pure-select dh-inline" name="category_id" required>
												<?=$category?>
											</select>
										</td>
										<td class="thumb-container" rowspan="5" align="center" valign="bottom" style="max-width:250px">
											<div class="thumb-img"><?php if($data['thumb']):?><img src="/<?=$data['thumb']?>" class="pure-img"><?php endif;?></div>
											<input id="thumb" name="thumb" value="<?=$data['thumb']?>" class="pure-input-1" type="text" placeholder="缩略图url">
										</td>
									</tr>
									<tr>
										<td><label for="title">标题 <span class="red">*</span></label></td>
										<td><input id="title" value="<?=$data['title']?>" class="pure-input-1" type="text" name="title" placeholder="标题" required></td>
									</tr>
									<tr>
										<td><label for="seo_title">seo标题</label></td>
										<td><input id="seo_title" value="<?=$data['seo_title']?>" class="pure-input-1" type="text" name="seo_title" placeholder="seo标题"></td>
									</tr>
									<tr>
										<td><label for="keywords">关键词</label></td>
										<td>
											<input id="keywords" value="<?=$data['keywords']?>" class="pure-input-1" type="text" name="keywords" placeholder="关键词">
											<span class="green">多个关键词用英文逗号分隔</span>
										</td>
									</tr>
									<tr>
										<td><label for="excerpt">摘要</label></td>
										<td>
											<textarea id="excerpt" class="pure-input-1" name="excerpt" placeholder="摘要"><?=$data['excerpt']?></textarea>
										</td>
									</tr>
									<tr>
										<td><label for="source">文章来源</label></td>
										<td colspan="2">
											<input id="source" class="pure-input-1" value="<?=$data['source']?>" type="text" name="source" placeholder="文章来源">
										</td>
									</tr>
									<tr>
										<td><label>售价</label></td>
										<td colspan="2">
											金钱：<input id="money" type="text" name="money" value="<?=$data['money']?>" placeholder="金钱数" class="dh-inline">&nbsp;&nbsp;
											金币：<input id="coin" type="text" value="<?=$data['coin']?>" name="coin" placeholder="金币数" class="dh-inline">
										</td>
									</tr>	
									<tr>
										<td><label for="content">内容 <span class="red">*</span></label></td>
										<td colspan="2">
											<textarea id="content" name="content" placeholder="内容" required><?=$data['content']?></textarea>
										</td>
									</tr>
								</table>
							</div>
							 <div class="tab-item" id="tab-b">
								 <table class="full pure-table pure-table-bordered">
									 <tr>
										 <td width="70"><label for="pid">上级</label></td>
										 <td>
											 <p>
                                                 <button type="button" class="pure-button btn-white btn-sm" id="juhe-parent-select" data="<?=url('admin/portal/search_juhe')?>?type=<?=$type?>">添加上级</button>
                                                 <span id="juhe-parent-container" class="juhe-container"><?=($parent?($parent['id'].'. '.$parent['title']).'<i class="juhe-cancel">&#215;</i>' :'无')?></span>
                                                 <input id="pid" type="hidden" value="<?=$data['pid']?>" name="pid" placeholder="父id">
                                             </p>
										 </td>
									 </tr>
									 <tr>
										 <td><labe>下级</labe></td>
										 <td>
											 <p><button type="button" class="pure-button btn-white btn-sm" id="juhe-children-select" data="<?=url('admin/portal/search_juhe')?>?type=<?=$type?>">添加下级</button></p>
											 <ul class="juhe-container" id="juhe-children-container">

											 </ul><?//=($children_ids ? implode(',',$children_ids) : '')?>
											 <input id="children_id" type="hidden" name="children_id" value="">

											 <?php if($children):?>
											 <ul id="juhe-children-existing" data="<?=url('admin/portal/del_pid')?>">
												 <h3 class="juhe-title">已存在的下级</h3>
												 <?php foreach ($children as $child):?>
													 <li id="children-data-<?=$child['id']?>"><span><?=$child['id']?></span>.<?=$child['title']?><i class="juhe-cancel" data="<?=$child['id']?>">&#215;</i><?php $children_id[]=$child['id'];?></li>
												 <?php endforeach; ?>
											 </ul>
											 <?php endif; ?>
										 </td>
									 </tr>
								 </table>
							 </div>
							<div class="tab-item" id="tab-c">
								<table class="full pure-table pure-table-bordered" id="table_file">
									<thead>
										<tr>
											<th width="120">名称</th>
											<th width="80">种类</th>
											<th>网址</th>
											<th>备注</th>
											<th width="120" align="center">操作</th>
										</tr>
									</thead>
									<?php $files=json_decode($data['files']);$num=1;if($files):?>
									<?php $num=count($files);foreach($files as $k => $item):?>
									<tr>
										<td><input class="pure-input-1" type="text" name="files[<?=$k?>][name]" value="<?=$item->name?>"></td>
										<td><input class="pure-input-1" type="text" name="files[<?=$k?>][type]" value="<?=$item->type?>"></td>
										<td><input class="pure-input-1" type="text" name="files[<?=$k?>][url]" value="<?=$item->url?>"></td>
										<td><input class="pure-input-1" type="text" name="files[<?=$k?>][remark]" value="<?=$item->remark?>"></td>
										<td><a href="javascript:;" class="pure-button btn-success btn-sm file_add">增加</a><a href="javascript:;" class="pure-button btn-warning btn-sm file_cancel">取消</a></td>
									</tr>
									<?php endforeach;?>
									<?php else:?>
									<tr>
										<td><input class="pure-input-1" type="text" name="files[0][name]"></td>
										<td><input class="pure-input-1" value="百度网盘" type="text" name="files[0][type]"></td>
										<td><input class="pure-input-1" type="text" name="files[0][url]"></td>
										<td><input class="pure-input-1" value="提取密码:" type="text" name="files[0][remark]"></td>
										<td><a href="javascript:;" class="pure-button btn-success btn-sm file_add">增加</a></td>
									</tr>
									<?php endif;?>
								</table>
							</div>
							 <div class="tab-item" id="tab-d">
								 <table class="full pure-table pure-table-bordered" id="table_more">
									 <thead>
									 <tr>
										 <th width="220">名称</th>
										 <th>值</th>
										 <th width="120" align="center">操作</th>
									 </tr>
									 </thead>
									 <?php $more=$data['more'] ? json_decode($data['more']) : '';$num_more=1;if($more):?>
										 <?php $num=count($more);foreach($more as $key => $value):?>
											 <tr>
												 <td><input class="pure-input-1" type="text" name="more[<?=$key?>][name]" value="<?=$value->name?>"></td>
												 <td><input class="pure-input-1" type="text" name="more[<?=$key?>][value]" value="<?=$value->value?>"></td>
												 <td><a href="javascript:;" class="pure-button btn-success btn-sm more_add">增加</a><a href="javascript:;" class="pure-button btn-warning btn-sm more_cancel">取消</a></td>
											 </tr>
										 <?php endforeach;?>
									 <?php else:?>
										 <tr>
											 <td><input class="pure-input-1" type="text" name="more[0][name]"></td>
											 <td><input class="pure-input-1" type="text" name="more[0][value]"></td>
											 <td><a href="javascript:;" class="pure-button btn-success btn-sm more_add">增加</a></td>
										 </tr>
									 <?php endif;?>
								 </table>
							 </div>
							 <div class="tab-item" id="tab-e">
								 <table class="full pure-table pure-table-bordered">
									 <tr>
										 <td width="70"><label for="status">状态</label></td>
										 <td>
											 <select id="status" name="status" class="pure-select">
												 <option value="0"<?=echo_select($data['status'],0)?>>隐藏</option>
												 <option value="1"<?=echo_select($data['status'],1)?>>公开</option>
											 </select>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="is_top">是否置顶</label></td>
										 <td>
											 <select id="is_top" name="is_top" class="pure-select">
												 <option value="0"<?=echo_select($data['is_top'],0)?>>否</option>
												 <option value="1"<?=echo_select($data['is_top'],1)?>>是</option>
											 </select>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="recommended">是否推荐</label></td>
										 <td>
											 <select id="recommended" name="recommended" class="pure-select">
												 <option value="0"<?=echo_select($data['recommended'],0)?>>否</option>
												 <option value="1"<?=echo_select($data['recommended'],1)?>>是</option>
											 </select>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="allow_comment">允许评论</label></td>
										 <td>
											 <select id="allow_comment" name="allow_comment" class="pure-select">
												 <option value="0"<?=echo_select($data['allow_comment'],0)?>>不允许</option>
												 <option value="1"<?=echo_select($data['allow_comment'],1)?>>允许</option>
											 </select>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="published_time">发布时间</label></td>
										 <td>
											 <input id="published_time" class="dh-inline" type="text" name="published_time" value="<?=date('Y-m-d H:i:s',$data['published_time'])?>" placeholder="发布时间">&nbsp;&nbsp;<i class="date-holder iconfont
icon-rili"></i>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="create_time">创建时间</label></td>
										 <td>
											 <input id="create_time" class="dh-inline" type="text" name="create_time" value="<?=date('Y-m-d H:i:s',$data['create_time'])?>" placeholder="创建时间">&nbsp;&nbsp;<i class="iconfont
icon-rili date-holder"></i>
										 </td>
									 </tr>
									 <tr>
										 <td width="70"><label for="recommended">计数</label></td>
										 <td>
											 查看次数:<input id="views" type="text" name="views" value="<?=$data['views']?>" placeholder="查看次数" class="dh-inline">&nbsp;&nbsp;
											 点赞次数:<input id="likes" type="text" name="likes" value="<?=$data['likes']?>" placeholder="点赞次数" class="dh-inline">&nbsp;&nbsp;
											 下载次数:<input id="downloads" type="text" name="downloads" placeholder="下载次数" value="<?=$data['downloads']?>" class="dh-inline">
										 </td>
									 </tr>
								 </table>
							 </div>
						</fieldset>
						<div class="" style="max-width:300px;margin:10px auto">
							<input type="hidden" name="type" value="<?=$type?>">
							<button type="submit" class="pure-button btn-custom pure-u-1">提交</button>
						</div>
					</form>
					<div class="msg" style="display:none"><?=$msg?></div>
					<div id="num" style="display:none"><?=$num?></div>
					<div id="num_more" style="display:none"><?=$num_more?></div>

			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<script type="text/javascript" src="/static/lib/My97DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="/static/lib/simditor-2.3.16/builds/styles/simditor.css" />
<script type="text/javascript" src="/static/lib/simditor-2.3.16/builds/script/all.js?v=aaa"></script>
<script type="text/javascript" src="/static/lib/layer/layer.js"></script>
<script charset="UTF-8" type="text/javascript">
	var currentData={id:<?=$data['id']?>,type:"<?=$type?>"};
</script>
<script type="text/javascript" src="/static/admin/pure/js/post.js"></script>

{%end%}