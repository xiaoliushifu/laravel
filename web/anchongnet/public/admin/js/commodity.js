/**
 * Created by lengxue on 2016/4/26.
 */
$(function(){
	//打开页面时即加载该信息
    //加载一级分类，无用吧？暂时注释掉
    var opt;
    var one0=0;
    //用于加载  ‘编辑'弹框里的 ‘商品分类’和‘配套信息’ 里的 一级分类信息，一箭双雕
    $.get("/getlevel",{pid:one0},function(data,status){
        for(var i=0;i<data.length;i++){
            opt="<option  value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
            $(".mainselect").append(opt);
        }
    });

    //编辑按钮的点击事件，弹框
    $(".edit").click(function() {
        $("#midselect").empty();	//是写错了吗？二级商品分类,及 配套信息 清空	
        $("#stock").empty();		//商品属性信息
        $("#sups").empty();			//配套商品
        $("#futuresups").empty();	//不知
        //获取商品id，该信息隐藏于属性data-id中，由控制器分配而来
        var id = $(this).attr("data-id");
        //商铺id（由控制器分配而来）
        var sid=$("#sid").val();

        var opt;
        var firstPid;
        //‘编辑’---图片信息--隐藏字段设置
        $("#gid").val(id);
        //修改请求地址,为更新商品信息做准备
        $("#updataForm").attr("action", "/commodity/" + id);
        
        //获取指定商品id的信息
        $.get("/commodity/" + id + "/edit", function (data, status) {
        	//商品分类
        	console.log(data);
            var arr=$.trim(data.type).split(" ");
            //清空一下
            $("#catarea").empty();
            for(var i=0;i<arr.length;i++){
            	//clone克隆出自己，但是去掉hidden,cateemplate这些class  ‘商品分类’
                var cat=$(".catemplate").clone().removeClass("hidden catemplate");
                // ‘商品分类’，可能有多个
                $("#catarea").append(cat);
                $("#flag").val(arr[i]);			//隐藏字段，有bug,既然循环，肯定会覆盖之前的设置的
                //又套了一层ajax,用于获取当前关联类别下的分类信息 有的商品的type字段里有多个值时。
                $.ajax({
                    type : "GET",
                    url : '/getsiblingscat?s='+new Date().getTime(),
                    data:{cid:arr[i]},		//?cid=65 
                    async:false,	//同步	
                    cache :false,
                    success : function(data){
                    	//获得相关分类下的类别名称
                        for (var j = 0; j < data.length; j++) {
                            opt = "<option  value=" + data[j].cat_id + ">" + data[j].cat_name + "</option>";
                            //二级分类，i是外层for循环的变量，即把二级分类信息，加载到对应的父类信息下
                            $(".midselect").eq(i+1).append(opt);
                        };
                        firstPid = data[0].parent_id;
                        var val=$("#flag").val();
                        //相关类的项，选中
                        $(".midselect").eq(i+1).find("option[value="+val+"]").attr("selected",true);
                        //设置父类选中项，也就是一级分类选中项
                        $(".mainselect").eq(i+1).find("option[value="+firstPid+"]").attr("selected",true);
                    }
                });	//内层ajax结束
            };//for循环结束

            $("#title").val(data.title);				//商品名称
            $("#description").val(data.desc);			//描述
            $("#remark").text(data.remark);				//备注
            $("#keyword").val(data.keyword);			//关键字
            $("#img").attr("src",data.images);			//详情图片
            //js插件？
            UE.getEditor('container').setContent(data.param);
            UE.getEditor('container1').setContent(data.package);

            //发送ajax请求获取当前商品的配套商品，需要单独的一个表goods_supporting
            $.get("/getsupcom",{"gid":id,'sid':sid},function(data,status){
                var sup;
                for(var i=0;i<data.length;i++){																				//只有当前商品，确实有对应的配套信息时，class=delsup才会生效，后续的删除事件才会绑定成功。
                    sup='<li class="list-group-item">'+data[i].goods_name+'<button type="button" data-id='+data[i].supid+' class="delsup btn btn-warning btn-xs pull-right glyphicon glyphicon-minus" title="删除条配套信息"></button></li>';
                    $("#sups").append(sup);
                }
            });
        });//最外层ajax结束

        /*----获取当前商品属性信息----*/
        var line;
        $.get('/getsiblingsattr', {gid: id}, function (data, status) {
            for (var i = 0; i < data.length; i++) {
                line = '<tr class="line"> <td> <input type="text" class="attrname form-control" value="' + data[i].name + '" /> </td> <td><textarea rows="5" class="attrvalue form-control">' + data[i].value + '</textarea> </td> <td> <button type="button" class="addcuspro btn-sm btn-link" title="添加"> <span class="glyphicon glyphicon-plus"></span> </button> <button type="button" class="savestock btn-sm btn-link" data-id="' + data[i].atid + '" title="保存"> <span class="glyphicon glyphicon-save"></span> </button> <button type="button" class="delcuspro btn-sm btn-link" title="删除" data-id="' + data[i].atid + '"> <span class="glyphicon glyphicon-minus"></span> </button> </td> </tr>';
                $("#stock").append(line);
            }
        });
    });//’编辑‘ 点击事件结束

    /*----弹框里--添加分类----*/
    $("body").on("click",".add button",function(){
        var tem=$(".catemplate").clone().removeClass("hidden").removeClass("catemplate");
        $("#catarea").append(tem);
    });

    /*----弹框里--删除分类----*/
    $("body").on("click",".minus button",function(){
        var len=$("#catarea").find(".form-group").length;
        if(len==1){
            alert("不能删除仅有的分类信息！");
        }else{
            $(this).parentsUntil("#catarea").remove();
        }
    });

    //添加配套商品，出现输入框
    $(".addsup").click(function(){
    	//三个输入框的，第一个选择框，已经第一次获取一级分类信息时加上了，一箭双雕的事情。
        var suptem=$(".suptemp").clone().removeClass("hidden").removeClass("suptemp");
        $("#futuresups").append(suptem);
    });

    //保存一条配套信息
    $("body").on("click",".save",function(){
    	//$(this).parentsUntil(".form-group")
    	//从父元素开始找，直到其第一个祖先元素的class有form-group就停止不找了，返回已经找到的祖先元素，这些祖先元素中，
    	//不包括class有form-group的。
        var goodsid=$(this).parentsUntil(".form-group").find(".supname").val();
        //find()方法,只找后代元素，无论是直接后代，还是间接后代都是寻找的范围。
        if(goodsid==""){
            alert("请选择商品");
        }else{
        	//图片信息部分，获得当初隐藏的,当前商品id
            var agid=$("#gid").val();
            var goodname=$(this).parentsUntil(".form-group").find(".goodsname").val();
            var gid=$(this).parentsUntil(".form-group").find(".gid").val();
            var title=$(this).parentsUntil(".form-group").find(".title").val();
            var price=$(this).parentsUntil(".form-group").find(".price").val();
            var img=$(this).parentsUntil(".form-group").find(".img").val();
            $.post("/goodsupporting",{"goodsid":goodsid,"goodsname":goodname,"gid":gid,"title":title,"price":price,"img":img,"agid":agid},function(data,status){
                alert(data.message);
                var sup='<li class="list-group-item">'+data.name+'<button type="button" data-id='+data.id+' class="delsup btn btn-warning btn-xs pull-right glyphicon glyphicon-minus" title="删除条配套信息"></button></li>';
                $("#sups").append(sup);
            })
        }
    });

    //删除一条配套商品信息，其实这里只是前台js的删除，其实数据库中仍然是存在的
    $("body").on("click",".delone",function(){
    	//$(this).parents会找到所有的祖先元素，从父元素，爷元素一直往上追溯。
    	//这里的parents还有了.form-group参数，相当于条件合并，class含有form-group的祖先元素
        $(this).parents(".form-group").remove();
    });

    /*----删除配套信息，仅在当前商品有配套信息时，才会执行绑定----*/
    $("body").on("click",".delsup",function(){
        if(confirm("确定要删除该条配套信息吗？")){
            $(this).parent().addClass("waitfordel");
            var id=$(this).attr("data-id");
            $.ajax({
                url: "/goodsupporting/" + id,
                type: 'DELETE',
                success: function (result) {
                    alert(result);
                    $(".waitfordel").remove();
                }
            });
        }
    });

    //添加配套商品的时候选择二级分类获取对应的分类下的商品
    $("body").on("change",".midforsup",function(){
        var val=$(this).val();
        var sid=$("#sid").val();
        $("#checked").attr("id","");
        $(this).parentsUntil(".form-group").find(".supname").attr("id","checked");
        $(this).parentsUntil(".form-group").find(".supname").empty().append(defaultopt);
        $.get("/getsibilingscommodity",{pid:parseInt(val),sid:sid},function(data,status){
            if(data.length==0){
                $("#checked").empty();
                $("#checked").append(nullopt);
            }else{
                for(var i=0;i<data.length;i++){
                    opt="<option  value="+data[i].goods_id+">"+data[i].title+"</option>";
                    $("#checked").append(opt);
                }
            }
        });
    });

    //添加配套商品的时候获取选中商品的第一条货品
    $("body").on("change",".supname",function(){
        var txt=$(this).find("option:selected").text();
        $(this).siblings(".goodsname").val(txt);
        var val=$(this).val();
        $(this).siblings(".supval").empty();
        $(".waitforspe").removeClass("waitforspe");
        $(this).addClass("waitforspe");
        $.get('/getsiblingsgood',{'good':val},function(data,status){
            var spe;
            if(data.length==0){
                spe='<input type="hidden" name="gid" value=" " class="gid"><input type="hidden" name="title" value=" " class="title"><input type="hidden" name="price" value=" " class="price"><input type="hidden" name="img" value=" " class="img">';
            }else{
                spe='<input type="hidden" name="gid" value='+data[0].gid+' class="gid"><input type="hidden" name="title" value="'+data[0].title+'" class="title"><input type="hidden" name="price" value='+data[0].goods_price+' class="price"><input type="hidden" name="img" value='+data[0].goods_img+' class="img">';
            }
            $(".waitforspe").siblings(".supval").append(spe);
        })
    });

    $("body").on("click", '.addcuspro', function () {
        var len = $(".line").length;
        var line = '<tr class="line"> <td> <input type="text" class="attrname form-control" /> </td> <td><textarea rows="5" class="attrvalue form-control" required></textarea> </td> <td> <button type="button" class="addcuspro btn-sm btn-link" title="添加"> <span class="glyphicon glyphicon-plus"></span> </button> <button type="button" class="savestock btn-sm btn-link" title="保存"> <span class="glyphicon glyphicon-save"></span> </button> <button type="button" class="delcuspro btn-sm btn-link" title="删除"> <span class="glyphicon glyphicon-minus"></span> </button> </td> </tr>';
        $("#stock").append(line);
    });

    $("body").on("click", '.savestock', function () {
        var aname = $(this).parentsUntil("#stock").find(".attrname").val();
        var avalue = $(this).parentsUntil("#stock").find(".attrvalue").val();
        if (aname == "") {
            alert("属性名不能为空！");
            $(this).parentsUntil("#stock").find(".attrname").focus();
        } else if (avalue == "") {
            alert("属性值不能为空！");
            $(this).parentsUntil("#stock").find(".attrvalue").focus();
        } else {
            var id = $(this).attr("data-id");
            var gid = $("#gid").val();
            $("#save").attr("id", "");
            $(this).attr("id", "save");
            $("#del").attr("id", "");
            $(this).siblings(".delcuspro").attr("id", "del");
            if (id == undefined) {
                $.ajax({
                    url: "/attr",
                    type: 'POST',
                    data: {gid: gid, name: aname, value: avalue},
                    success: function (response) {
                        alert(response.message);
                        $("#save").attr("data-id", response.id);
                        $("#del").attr("data-id", response.id);
                    }
                });
            } else {
                $.ajax({
                    url: "/attr/" + id,
                    type: 'PUT',
                    data: {gid: gid, name: aname, value: avalue},
                    success: function (response) {
                        alert(response.message);
                    }
                });
            }
        }
    });

    $("body").on("click", '.delcuspro', function () {
        if (confirm("你确定要删除该条属性信息吗？")) {
            var id = $(this).attr("data-id");
            $(this).parents(".line").addClass("waitfordel");
            $.ajax({
                url: "/attr/" + id,
                type: 'DELETE',
                success: function (result) {
                    alert(result);
                    $(".waitfordel").remove();
                }
            });
        }
    });

    var nullopt="<option value=''>无数据，请重选上级分类</option>";
    var defaultopt="<option value=''>请选择</option>";
    $("body").on("change",".mainselect",function(){
        var val=$(this).val();
        $(".waitforopt").removeClass("waitforopt");
        $(this).parent().siblings("div").find(".midselect").empty().addClass("waitforopt");
        $(this).parent().siblings("div").find(".midselect").append(defaultopt);
        if(val==""){
        }else{
            $.get("/getlevel",{pid:parseInt(val)},function(data,status){
                if(data.length==0){
                    $(".waitforopt").find(".midselect").empty();
                    $(".waitforopt").append(nullopt);
                }else{
                    for(var i=0;i<data.length;i++){
                        opt="<option  value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
                        $(".waitforopt").append(opt);
                    }
                }
            });
        }
    });

    //建立一個可存取到該file的url
    function getObjectURL(file) {
        var url = null ;
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    }

    /*----事件转移----*/
    $("body").on("click",'.gallery',function(){
        $(this).siblings(".pic").click();
    });

    /*----替换详情图片的操作----*/
    $("body").on("change",'.pic',function(){
        var gid=$("#gid").val();
        if(confirm('你确定要替换这张图片吗？')){
            var objUrl = getObjectURL(this.files[0]) ;
            if (objUrl) {
                $(".isEdit").removeClass("isEdit");
                $(this).siblings(".gallery").find(".img").addClass("isEdit");
                $("#formToUpdate").ajaxSubmit({
                    type:'post',
                    url:'/updataimg',
                    data:{gid:gid},
                    success:function(data){
                        alert(data.message);
                        if(data.isSuccess==true){
                            $(".isEdit").attr("src",objUrl);
                        }
                    }
                });
            }
        }
    });
});
