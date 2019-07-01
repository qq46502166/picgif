$(function(){
    $("#admin-portal-"+currentData.type).show();
    Jtab('#jtab_inline');
    showMsg('.msg');
    //富文本编辑器
    var editor = new Simditor({
        textarea: $('#content'),
        toolbar: ['title','bold', 'italic', 'color','link', '|', 'ol', 'ul','code','|','html','fullscreen']
    });
    /*$("#myform").submit(function(){
     editor.setValue(editor.getValue());
     return true;
     });*/
    //var he = HE.getEditor('editor');
    /*绑定点击事件到.file_add*/
    var file_num=$('#num');
    $(document).on('click','.file_add',function(){
        $(this).parents('tr').after(getItem());
        file_num.html(parseInt(file_num.html())+1);
    });
    /*绑定点击事件到.file_cancel*/
    $(document).on('click','.file_cancel',function(){
        $(this).parents('tr').remove();
        file_num.html(parseInt(file_num.html())-1);
    });
    /*绑定点击事件到.file_add*/
    var more_num=$('#num_more');
    $(document).on('click','.more_add',function(){
        $(this).parents('tr').after(getMoreItem());
        more_num.html(parseInt(more_num.html())+1);
    });
    /*绑定点击事件到.file_cancel*/
    $(document).on('click','.more_cancel',function(){
        $(this).parents('tr').remove();
        more_num.html(parseInt(more_num.html())-1);
    });
    /*绑定点击事件到 .js-check-all*/
    /*$(document).on('click','.layui-layer .js-check-all',function(){
     var _this=$(this);
     if(_this.is(':checked')){
     _this.parents('table').find('td >input[type="checkbox"]').prop('checked', true);
     }else{
     _this.parents('table').find('td >input[type="checkbox"]').prop('checked', false);
     }
     });*/

    /*缩略图*/
    $("#thumb").blur(function(){
        var _this=$(this);
        var url=_this.val();
        if(url !==''){
            if(/^https?:\/\/.*$/i.test(url)){
                if(/^https?:\/\/([a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?\.)+[a-z]{2,6}\/.*?(jpg|png|gif|jepg)$/i.test(url)){
                    //外部图片https://img.alicdn.com/imgextra/i3/1120328108/TB2QwHvD3mTBuNjy1XbXXaMrVXa_!!1120328108-0-item_pic.jpg_430x430q90.jpg
                    _this.siblings('.thumb-img').html('<img src="'+_this.val()+'" class="pure-img">');
                }
            }else if(/^\/?[\w\.\-]+\/.*?(jpg|png|gif|jepg)$/i.test(url)){
                //本地图片 uploads/images/demo.png
                url=url.replace(/^\//,'');
                _this.val(url);
                _this.siblings('.thumb-img').html('<img src="/'+_this.val()+'" class="pure-img">');
            }
        }
    });

    //日期选择器
    $('.date-holder').on('click', function(e){
        WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:($(this).siblings('input')[0].id)});
    });
    /**---------------聚合----------------**/
    //上级聚合 点击后->弹出框架，输入关键词，搜索->在搜索结果选择->把选择的放到这里
    $('#juhe-parent-select').click(function () {
        var url=$(this).attr('data');
        layer.prompt({
            formType: 0,
            value: '',
            'title':'输入关键词',
            maxlength: 140,
        },function (value, index) {
            var pid=parseInt($('#pid').attr('value'));
            $.post(url,{key:value,id:currentData.id,pid:pid},function (data) {
                layer.open({
                    type:1,
                    title:'请选择下级：支持多个',
                    content:data.status==0 ? formatSearchResult(data.data):data.msg,
                    success: function(layero, sindex){
                        if(layero.find('.sul').length >0){
                            layer.close(index);
                        }else {
                            layer.msg(data.msg);
                            layer.close(sindex);
                        }
                    },
                    btn:['确定', '取消'],
                    yes:function (sindex,layero) {
                        var theCheckBox=layero.find('.scheck:checked');
                        var i=0;
                        data=data.data;
                        if(theCheckBox.length <1)
                            layer.msg('最少要选中一项');
                        else {
                            i=parseInt(theCheckBox.attr('value'));
                            $('#juhe-parent-container').html(data[i].id+'. '+data[i].title+'<i class="juhe-cancel">&#215;</i>');
                            $('#pid').attr('value',data[i].id);
                            layer.close(sindex);
                        }
                    }
                });
            });
        });
    });
    $('#juhe-children-select').click(function () {
        var url=$(this).attr('data');
        layer.prompt({
            formType: 0,
            value: '',
            'title':'输入关键词',
            maxlength: 140,
        },function (value, index) {
            var pid=parseInt($('#pid').attr('value'));
            $.post(url,{key:value,id:currentData.id,pid:pid},function (data) {
                layer.open({
                    type:1,
                    title:'请选择一个作为上级',
                    content:data.status==0 ? formatSearchResult(data.data,true):data.msg,
                    success: function(layero, sindex){
                        if(layero.find('.sul').length >0){
                            layer.close(index);
                        }else {
                            layer.msg(data.msg);
                            layer.close(sindex);
                        }
                    },
                    btn:['确定', '取消'],
                    yes:function (sindex,layero) {
                        var theCheckBox=layero.find('.scheck:checked');;
                        if(theCheckBox.length <1)
                            layer.msg('最少要选中一项');
                        else {
                            var html='';
                            data=data.data;
                            var childrenSelector=$('#children_id');
                            var children_id=childrenSelector.attr('value');
                            theCheckBox.each(function () {
                                var i=parseInt(this.value);
                                html+='<li id="children-data-'+data[i].id+'"><span>'+data[i].id+'</span>. '+data[i].title+'<i class="juhe-cancel" data="'+data[i].id+'">&#215;</i></li>';
                                children_id+=','+data[i].id;
                            });
                            $('#juhe-children-container').append(html);
                            childrenSelector.attr('value',children_id.replace(/^,/,''));
                            layer.close(sindex);
                        }
                    }
                });
            });
        });
    });

    //监控上级聚合的取消
    $('#juhe-parent-container').on('click','.juhe-cancel',function () {
        $('#juhe-parent-container').html('');
        $('#pid').attr('value',0);
    });
    //监控下级聚合的取消
    $('#juhe-children-container').on('click','.juhe-cancel',function () {
        var id=this.getAttribute('data');
        $("#juhe-children-container").children('#children-data-'+id).remove();
        var childrenSelector=$('#children_id');
        var children_ids=childrenSelector.attr('value');
        var reg=new RegExp('^'+id+'(,|$)|,'+id+'(,|$)');
        childrenSelector.attr('value',children_ids.replace(reg,''));
    });
    //监控已经存在的下级聚合的取消
    $('#juhe-children-existing').on('click','.juhe-cancel',function () {
        var id=this.getAttribute('data');
        var childrenExistingSelector=$("#juhe-children-existing");
        var url=childrenExistingSelector.attr('data');
        $.get(url+'?id='+id,function () {
            childrenExistingSelector.children('#children-data-'+id).remove();
        });
    });
});
function formatSearchResult(data,multi) {
    var onclickFunc='';
    if(multi)
        onclickFunc='changSelectMulti(this)';
    else
        onclickFunc='changSelect(this)';
    var html='<table class="pure-table sul">';
    for(var i=0;i<data.length;i++){
        html+='<tr>';
        html+='<td class="std"><input type="checkbox" value="'+i+'" class="scheck" onclick="'+onclickFunc+'"></td>';
        html+='<td class="sid">'+data[i].id+'</td>';
        html+='<td class="stitle">'+data[i].title+'</td>';
        html+='</tr>';
    }
    return html+'</table>';
}
function changSelect(current) {
    $('.scheck').prop('checked', false);
    $(current).prop('checked', true);
}
function changSelectMulti(current) {
    var _this=$(current);
    if(_this.is(':checked')){
        _this.prop('checked', true);
    }else {
        _this.prop('checked', false);
    }
}

function getItem(){
    var file_i=$('#num').html();
    return '<tr><td><input class="pure-input-1" type="text" name="files['+file_i+'][name]"></td><td><input value="百度网盘" class="pure-input-1" type="text" name="files['+file_i+'][type]"></td><td><input class="pure-input-1" type="text" name="files['+file_i+'][url]"></td><td><input value="提取密码:" class="pure-input-1" type="text" name="files['+file_i+'][remark]"></td><td><a href="javascript:;" class="pure-button btn-success btn-sm file_add">增加</a><a href="javascript:;" class="pure-button btn-warning btn-sm file_cancel">取消</a></td></tr>';
}

function getMoreItem () {
    var more_i=$('#num_more').html();
    var html='<tr>';
    html+='<td><input class="pure-input-1" type="text" name="more['+more_i+'][name]"></td>';
    html+='<td><input class="pure-input-1" type="text" name="more['+more_i+'][value]"></td>';
    html+='<td><a href="javascript:;" class="pure-button btn-success btn-sm more_add">增加</a><a href="javascript:;" class="pure-button btn-warning btn-sm more_cancel">取消</a></td>';
    html+='</tr>';
    return html;
}
