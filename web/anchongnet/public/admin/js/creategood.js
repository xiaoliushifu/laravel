/**
 * Created by lengxue on 2016/4/20.
 */
$(function(){
    /*-------------------------------------------基本信息---------------------------------------------*/
	/**
	 * 页面初始化，默认加载一级分类信息
	 */
    var opt;
    var one0=0;
    var defaultopt="<option value=''>请选择</option>";
    var nullopt="<option value=''>无数据，请重选上级分类</option>";
    $.get("/getlevel",{pid:one0},function(data,status){
        for(var i=0;i<data.length;i++){
            opt="<option  value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
            $("#mainselect").append(opt);
        }
    });

    /**
     * 一级分类 点击事件
     */
    $("#mainselect").change(function(){
        var val=$(this).val();				//一级分类id
        $("#midselect").empty();			//清空二级分类候选项
        $("#midselect").append(defaultopt);
        $("#name").empty();					//商品表单项旁边的隐藏域
        $("#name").append(defaultopt);
        if(val==""){

        }else{
        	/**
        	 * ajax请求加载二级分类信息
        	 */
            $.get("/getlevel",{pid:parseInt(val)},function(data,status){
                if(data.length==0){
                    $("#midselect").empty();
                    $("#midselect").append(nullopt);
                }else{
                    for(var i=0;i<data.length;i++){
                        opt="<option  value="+data[i].cat_id+">"+data[i].cat_name+"</option>";
                        $("#midselect").append(opt);
                    }
                }
            });
        }
    });

    /**
     * 为二级分类 ，设置change事件
     */
    $("#midselect").change(function(){
        var val=$(this).val();				//二级分类id
        var sid=$("#sid").val();			//商铺id,由控制器分配而来
        $("#name").empty();					//商品表单项旁边的隐藏域
        $("#name").append(defaultopt);
        $("#checks").empty();				//分类标签置空
        $("#attrs").empty();				//
        /**
         * 根据二级分类信息，请求分类下的商品信息
         */
        $.get("/getsibilingscommodity",{pid:parseInt(val),sid:sid},function(data,status){
            if(data.length==0){
                $("#name").empty();
                $("#name").append(nullopt);
            }else{
            	//该二级分类下的商品，都加载到 “商品表单项目”
                 for(var i=0;i<data.length;i++){
                     opt="<option  value="+data[i].goods_id+">"+data[i].title+"</option>";
                     $("#name").append(opt);
                 }
            }
        });
        /**
         * 加载商品分类下的商品标签信息
         */
        $.get("/getsiblingstag",{cid:val},function(data,status){
            for(var i=0;i<data.length;i++){
                var lab='<label><input type="checkbox" name="tag[]" value='+data[i].tag+'>'+data[i].tag+'</label>&nbsp;&nbsp;&nbsp;';
                $("#checks").append(lab);
            }
        })
    });

    /**
     * 选择商品  change事件
     */
    $("#name").change(function(){
        var val=$(this).val();					//选中项商品id
        var li;
		var optioner;
        var txt=$("#name option:selected").text();	//选中项商品名
        alert(txt);
        $("#attrs").empty();						//商品属性
        $("#commodityname").val(txt);				//隐藏域设置，选中项商品名
        /**
         * ajax，根据商品id,请求获取商品的属性信息
         */
        $.get("/getsiblingsattr",{gid:parseInt(val)},function(data,status){
            for(var i=0;i<data.length;i++){
			    $("#selectforattr").attr("id","");				//暂时未找到啊
			    //商品属性，用li列表实现
                li='<li> <label class="col-sm-2 control-label">'+data[i].name+'</label> <div class="col-sm-3"> <select class="form-control" name="attr[]" required id="selectforattr"> </select> </div> </li> <div class="clearfix"><br><br></div>';
                $("#attrs").append(li);
                /**
                 *属性值，可能有多个，需要拆分 
                 *$("#selectforattr")暂无
                 */
                var arr=data[i].value.split(" ");
                for(var j=0;j<arr.length;j++){
					optioner='<option value='+arr[j]+'>'+arr[j]+'</option>';
					$("#selectforattr").append(optioner);
                }
            }
        });
        
        /**
         * 获取商品的关键字和关联分类信息
         * 虽然只要这两个数据，但是后台的控制器是select *，影响性能
         */
        $.get("/commodity/"+val,function(data,status){
            $("#keyword").val(data.keyword);
            $("#type").val(data.type);
        });
    });
    
    /**
     * 添加一条库存记录
     */
    $("body").on("click",'.addcuspro',function(){
        var len=$(".line").length;
        var line='<tr class="line"> <td> <input type="text" name="stock[region][]" class="form-control" required /> </td><td> <input type="text" name="stock[location][]" class="form-control" required /> </td> <td> <input type="text" name="stock[shelf][]" class="form-control" required /> </td> <td> <input type="number" min="0" name="stock[num][]" class="form-control" required /> </td> <td> <button type="button" class="addcuspro btn-sm btn-link" title="添加"> <span class="glyphicon glyphicon-plus"></span> </button> <button type="button" class="delcuspro btn-sm btn-link" title="删除"> <span class="glyphicon glyphicon-minus"></span> </button> </td> </tr>';
        $("#stock").append(line);
    });

    /**
     * 删除一条库存记录
     */
    $("body").on("click",'.delcuspro',function(){
        var len=$(".line").length;
        if(len>1){
            $(this).parents(".line").remove();
        }
    });
});

/**
 * 货品图片的上传处理
 * 使用了diyUpload插件
 */
$('#test').diyUpload({
    url:'/thumb',
    success:function( data ) {
        console.info( data.message );
        var len=$("#img").find("li").length;
        var lis='<li> <input type="hidden" name="pic['+len+'][url]" value="'+data.url+'"> </li>';
        $("#img").append(lis);
    },
    error:function( err ) {
        console.info( err );
    },
});

