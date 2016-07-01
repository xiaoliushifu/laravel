/**
 * Created by lengxue on 2016/4/26.
 */

/**
 * 获取一级分类信息
 * 将在两处地方使用：① 商品分类里的一级分类select列表    ② 商品配套信息的一级分类select列表
 */
$(function(){
    var opt;
    var one0=0;
    var defaultopt="<option value=''>请选择</option>";
    var nullopt="<option value=''>无数据，请重选上级分类</option>";
    $.get("/getlevel",{pid:one0},function(data,status){
        for(var i=0;i<data.length;i++){
            opt="<option  value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
            $(".mainselect").append(opt);
        }
    });

    $("body").on("change",".mainselect",function(){
        var val=$(this).val();
        $(".waitforopt").removeClass("waitforopt");
        $(this).parent().siblings("div").find(".midselect").empty().addClass("waitforopt");
        $(this).parent().siblings("div").find(".name").empty();
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

    /*----添加一条    商品分类----*/
    $("body").on("click",".add button",function(){
        var tem=$(".catemplate").clone().removeClass("hidden").removeClass("catemplate");
        $("#catarea").append(tem);
    });

    /*----删除一条  商品分类----*/
    $("body").on("click",".minus button",function(){
    	//这里有个bug,本意是至少保留一个商品所属分类信息，但是$(".mainselect")会获取到多个，它们来自于
    	//  ①页面初始化的商品分类   ② 商品配套信息里的分类（两个，一个隐藏，一个非隐藏）
        var len=$(".mainselect").length;
        if(len<3){
            alert("不能删除仅有的分类信息！");		//至少有一行分类信息
        }else{
            $(this).parents(".form-group").remove();
        }
    });

    /**
     *  ‘添加属性’ 点击事件
     */
    $("body").on("click",'.addcuspro',function(){
        var len=$(".line").length;
        var line='<tr class="line"> <td> <input type="text" name="attrname[]" class="form-control" required /> </td> <td> <textarea name="attrvalue[]" class="form-control" required rows="5"></textarea> </td> <td> <button type="button" class="addcuspro btn-sm btn-link" title="添加"> <span class="glyphicon glyphicon-plus"></span> </button> <button type="button" class="delcuspro btn-sm btn-link" title="删除"> <span class="glyphicon glyphicon-minus"></span> </button> </td> </tr>';
        $("#stock").append(line);
    });

    /**
     * ‘删除属性’ 点击事件
     */
    $("body").on("click",'.delcuspro',function(){
        var len=$(".line").length;
        if(len>1){
            $(this).parents(".line").remove();
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
    	//text是商品名，配套的商品名
        var txt=$(this).find("option:selected").text();
        //设置隐藏项goodsname的value为supname选中项目的商品名称
        $(this).siblings(".goodsname").val(txt);
        var val=$(this).val();						//当前选中项的value,value是id,text是配套商品名
        $(this).siblings(".supval").empty();
        //添加商品页面，尚未发现class有waitforspe的html元素
        $(".waitforspe").removeClass("waitforspe");
        $(this).addClass("waitforspe");
        //传入配套商品名，请求获取与配套商品相关的货品信息，这里有问题，应该获取不到
        //因为后台控制器的方法去查询时，错把名字当做id查询
        $.get('/getsiblingsgood',{'good':val},function(data,status){
            var spe;
            if(data.length==0){
                spe='<input type="hidden" name="gid[]" value=" "><input type="hidden" name="title[]" value=" "><input type="hidden" name="price[]" value=" "><input type="hidden" name="img[]" value=" ">';
            }else{
                spe='<input type="hidden" name="gid[]" value='+data[0].gid+'><input type="hidden" name="title[]" value="'+data[0].title+'"><input type="hidden" name="price[]" value='+data[0].goods_price+'><input type="hidden" name="img[]" value='+data[0].goods_img+'>';
            }
            $(".waitforspe").siblings(".supval").append(spe);
        })
    });

    //添加配套商品
    $("body").on("click",".addsup",function(){
        var suptem=$(".suptemp").clone().removeClass("hidden").removeClass("suptemp");
        //使用before方法，相当于插入哥哥或者姐姐节点，这里所谓的节点一般是html代码
        $("#img").before(suptem);
    });

    //删除配套商品
    $("body").on("click",".minusup",function(){
        $(this).parents(".form-group").remove();
    })
});

/*商品图片添加*/
/**
 * diyUpload - jQuery多张图片批量上传插件
 */
$('#detail').diyUpload({
    url:'/img',
    formData:{
        imgtype:1
    },
    success:function( data ) {
        console.info( data.message );
        var len=$("#img").find("li").length;
        var lis='<li> <input type="hidden" name="pic['+len+'][url]" value="'+data.url+'"> <input type="hidden" name="pic['+len+'][imgtype]" value="1"> </li>';
        $("#img").append(lis);
    },
    error:function( err ) {
        console.info( err );
    },
});