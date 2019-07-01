{%extend@common/base%}

{%block@main%}
<div class="path">
	<a href="<?=url('admin/index/index')?>">首页</a> &gt; 门户管理 &gt; <?=$pindao[$type]?>频道 &gt; <?=$title?>
</div>
<div class="content">
	<div class="content-card">
		<div class="content-detail">
			<div class="content-button">
				<a href="<?=url('admin/portal/category_add')?>?type=<?=$type?>" class="pure-button btn-custom btn-sm">添加分类</a>
				<a href="<?=url('admin/portal/update_category_cache')?>?type=<?=$type?>" class="pure-button btn-custom btn-sm update-cache">更新缓存</a>
			</div>
			<div class="content-item">
				<form class="pure-form">
				<table class="full pure-table pure-table-bordered">
					<thead>
						<tr>
							<td width="50">分类id</td>
							<td>分类名称</td>
							<td>分类描述</td>
							<td width="120" align="center">操作</td>
						</tr>
					</thead>
					<tbody>
						<?=$category?>
					</tbody>
				</table>
				</form>
			</div>
		</div>
	</div>
</div>
{%end%}

{%block@javascript%}
<style>
#jtab label{margin-top:16px !important;margin-bottom:8px !important;}
</style>
<script charset="UTF-8" type="text/javascript">
var url_edit="<?=url('admin/portal/category_edit')?>?type=<?=$type?>",
		url_delete="<?=url('admin/portal/category_delete')?>?type=<?=$type?>";
$(function(){
	$("#admin-portal-category-<?=$type?>").addClass('active-this');
	doit(".edit",url_edit);
	doit(".delete",url_delete);
	//更新缓存
	var someClick=new clickClass();
	someClick.ajaxLink('.update-cache');
});

function doit(elme,url){
	$("table "+elme).click(function(){
		_this=$(this);		
		if(elme=='.edit'){
			_this.attr('href',url + '&id='+ _this.attr('data'));
			return true;
		}else if(elme=='.delete'){
			if( confirm("你真的要删除这个分类？")){
				var ajax=new Jajax();
				ajax.get(url + '&id='+ _this.attr('data'),'', function(data){
					_this.parents('tr').remove();
					return true;
				}, false, true,function(data){
					//console.log(data);
					alert(data.msg);
				});
			}
			return false;
		}
	});
}
</script>
{%end%}