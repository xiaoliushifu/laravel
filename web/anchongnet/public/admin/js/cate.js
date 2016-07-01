/**
 * Created by lengxue on 2016/4/18.
 */
$(function(){
	//绑定 ‘编辑’按钮的点击事件
    $(".edit").click(function(){
        var id=parseInt($(this).attr("data-id"));
        //修改提交地址
        $("#myform").attr("action","/goodcate/"+id);
        //ajax后台传输，用弹框方式在当前页面给个编辑小框
        $.get("/goodcate/"+id+"/edit",function(data,status){
        	//弹框的前三项
            $("#catname").val(data.cat_name);
            $("#keyword").val(data.keyword);
            $("#description").val(data.cat_desc);

            switch (data.is_show){
                case 0:
                	$("#show0").attr('checked',true);
                    break;
                case 1:
                	$("#show1").attr('checked',true);
                    break;
            }
        })
    });
    
    //再次绑定另一个点击事件,弹框的第四项 父级分类，可以考虑不显示该项目
    $(".edit").click(function(){
        $("#par0").empty();
        var defaultopt="<option value='0'>无</option>";
        $("#par0").append(defaultopt);
        var opt;
        var pid=$(this).attr("data-pid");
        var one0=0;
        $.get("/getlevel",{pid:one0},function(data,status){
            for(var i=0;i<data.length;i++){
                opt="<option class='opt' value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
                $("#par0").append(opt);
            }
            $(".opt[value='" + pid + "']").attr("id","cur");
            $("#cur").selected=true;
        });
    });

    //查看子分类
    $(".view").click(function(){
        $("#soncate").empty();
        var id=parseInt($(this).attr("data-id"));
        var dt;
        var url;
        switch (parseInt($(this).attr("data-pid"))){
            case 0:
                url="/getlevel";		//获取顶级分类信息的url
                break;
            default:
                url="/getlevel2";		//获取非顶级信息的url
                break;
        }
        //ajax get方式获得
        $.get(url,{pid:id},function(data,status){
            //alert(data.length);
            for(var i=0;i<data.length;i++){
                dt="<dt class='son' data-id="+data[i].cat_id+">"+data[i].cat_name+"</dt>";
                $("#soncate").append(dt);
            }
        })
    });

    // '删除' 按钮
    $(".del").click(function(){
        if(confirm('确定要删除吗？')){
            var id=$(this).attr("data-id");
            //既不是get,也不是post的，而是delete，所以使用$.ajax
            $.ajax({
                url: "/goodcate/"+id,
                type: 'DELETE',			//ajax方式，可以使用http原始的delete方法吗？，可以的！
                success: function(result) {
                    // Do something with the result
                    alert(result);
                    setTimeout(function(){location.reload()},500);
                }
            })
        }
    });
});