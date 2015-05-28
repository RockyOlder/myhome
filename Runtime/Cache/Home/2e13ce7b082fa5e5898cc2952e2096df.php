<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>无标题文档</title>

        <link href="/myhome/App/Home/View/Public/Css/style.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Js/jquery-ui/css/pepper-grinder/jquery-ui.min.css">
            <link id="artDialogSkin" href="/myhome/App/Home/View/Public/Css/skin/aero/aero.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Css/bootstrap.min.css"> 
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/common.js"></script>
            <script type="text/javascript" src ="/myhome/App/Home/View/Public/ueditor/editor_config.js"></script>
            <script type="text/javascript" src ="/myhome/App/Home/View/Public/ueditor/editor_all_min.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/artDialog.js"></script>
            <link rel="stylesheet" href="/myhome/App/Home/View/Public/Css/uploadify.css">
                <script src='/myhome/App/Home/View/Public/Js/jquery.uploadify.min.js'></script>
                <script type="text/javascript">
                    $(function(){

                    //    if($(".house_into").val()==''){ $(".house_into").text("请选择")}
                        function setout(){
                            $('.validateTips').text()
                            $('#skuNotice').show();
                            var dingshi= setTimeout( function(){
                                $( '#skuNotice' ).fadeOut();
                            }, ( 1 * 1000 ) );  
                            return dingshi;
                        } 
                        function checkInput(){
                            var bValid = true;
                            bValid = bValid && checkLength( $("#goods_name"), "商品名字", 4, 128 );
//                                
//                            bValid = bValid && checkEmpty( $("#type_on"), "\u8bf7选择分类！" );
//                            $("#type_on").removeClass('ui-state-error')
//                            //      bValid = bValid && checkLength( $("#type_on"), "\u7528户名", 2, 16 );sales//ui-state-error
//                            //   bValid = bValid && checkEmpty( $("#type_n"), "\u8bf7选择商家名称！" );
//                                
//                            //      bValid = bValid && checkEmpty( $("#marque"), "\u8bf7选择商品型号！" );
//                            //  $("#marque").removeClass('ui-state-error')
//                            bValid = bValid && checkRegexp( $("#price"), /([0-9])+$/i, "价格只能是数字组成" );
//                            bValid = bValid && checkRegexp( $("#inventory"), /([0-9])+$/i, "库存只能是数字组成" );
//                            bValid = bValid && checkEmpty( $("#addressAdd"), "\u8bf7选择省市！" );
//                            //    bValid = bValid && checkEmpty( $("#city_list"), "\u8bf7选择市区！" );
//                            $("#addressAdd").removeClass('ui-state-error')
//                            bValid = bValid && checkEmpty( $("#val_list"), "\u8bf7选择区县！" );
//                            $("#val_list").removeClass('ui-state-error') 
//                            //  bValid = bValid && checkEmpty( $("#description"), "商家描述不能为空！" );
//                            $("#description").removeClass('ui-state-error') 
                            if(bValid==false){ setout(); }
                            return bValid;
                        }
//                        $('form').submit(function(){
//                            if( $("#type_on").val()==0){
//                                artDialog({content:'父栏目无法选择,请选择二级分类',width:300,height:150})
//                                return false;
//                            }
//                            if(!checkInput()){
//                                $('.dfinput').each(function () {
//                                    if($(this).val()==''){
//                                        $(this).next().css("color","red");
//                                        $('.errorColor').css("color","red")
//                                    }
//                                });
//                                return false;
//                            }
//                            return true
//                        })

                            
                        //                        $(".top_cate").each(function () {
                        //                            var top_cate=$(this).text();
                        //                            var parent= top_cate.substr(-1,1);
                        //                            $(this).text(top_cate.substring(0,top_cate.length-1))
                        //                        })
                            
//                        $("#type_on").bind("change",function(){
//                           
//                            if($(this).val()==0){
//                                artDialog({content:'父栏目无法选择,请选择二级分类',width:300,height:150})
//                            }       
//                            
//                            //alert($(this).text())
//                        })  
                        /*   $('#village_id').bind('change',function(){
                                $(this).parent().next().css("color","#7f7f7f")
                            })
                         */
                        $(".dfinput").bind("focus",function(){
                            //   alert(1)
                            $('#skuNotice').hide();
                            $(this).addClass("focus");
                            $(this).next().css("color","#7f7f7f");
                            if($(this).hasClass("ui-state-error")){
                                $(this).removeClass( "ui-state-error" );
                                $(".validateTips").removeClass("errorTip").hide();	
                            }
                        }).bind("blur",function(){
                            $(this).removeClass("focus");
                            if($(this).val()==''){ 
                                $(this).next().css("color","red"); }
                            checkInput();
                        });
                    })
                </script>
                <style type="text/css">
                    .pro{  float: left;line-height: 30px; margin-left: 0px;margin-bottom: 10px;}

                    .sku_tip { background: none repeat scroll 0 0 rgba(0, 0, 0, 0.7);border-radius: 4px;box-shadow: 0 0 3px 3px rgba(150, 150, 150, 0.7);color: #fff;display: none;left: 40%;margin-left: -70px; padding: 5px 10px;position: fixed; text-align: center; top: 50%;z-index: 9999;}
                    .pro select{width: 345px;height: 32px; }
                    .box{ margin-left: 5px; font-size: 12px; margin-top: -3px; padding-left:5px; padding:3px;}
                    #detailDialog{}
                    #tableAdd{ margin-top: 10px;}
                    .imgSave{ float: left; margin-left:-140px; margin-top:-50px; position: absolute;}
                    .spanSave{margin-left:-3%; margin-top:-27px; float: left; position: absolute; font-size: 16px;}
                    .placeul li a{ color:#428bca; }
                    #detailDialog {
                        position:absolute;
                        height:auto;
                        margin-left :626px; 
                        margin-top: 38px;
                        width: 500px;
                        display:none;
                        /* -webkit-box-shadow: 0 0 10px #121a2a;
                         box-shadow: 0 0 20px #121a2a;
                         border-top: solid 1px #a7b5bc;
                         border-left: solid 1px #a7b5bc;
                         border-right: solid 1px #ced9df;
                         border-bottom: solid 1px #ced9df;*/

                        padding:20px;
                        background-color:#FFF;
                        cursor:move;
                        z-index: 9999;
                    }
                    #detailDialog #close {
                        position:absolute;
                        right:10px;
                        top:8px;
                        width:15px;
                        height:15px;
                        line-height:11px;
                        border:1px solid rosybrown;

                        cursor:pointer
                    }
                    #detailDialog table,#detailDialog tr,#detailDialog td{
                        border-color:#d9d6c4;
                        background-color:#FFF;
                        cursor:default
                    }
                    #detailDialog table {
                        width:100%;
                    }
                    #detailDialog tr,#detailDialog td {
                        height:40px;
                    }
                    #detailDialog td span {
                        margin-left:10px;
                        margin-right:10px;
                        cursor:text
                    }
                    #detailDialog td{
                        text-align:left;
                    }
                    #detailDialog .label{
                        font-weight:600;
                        color:#654b24;
                        text-align:center;
                    }
                    #detailDialog .closeHover {
                        background-color:#f391a9;
                        color:#FFF
                    }
                    #detailDialog .closeMouseDown {
                        background-color:#aa2116;
                        color:#f6f5ec
                    }
                    #select_today{
                        float: left;
                    }
                    .img_poaddtion{ margin-left: 20px; width: 100px; float: left;}
                    .close{}

                </style>
                </head>
                <body style="background: none;">

                    <div class="place">
                        <span>位置： </span>
                        <ul class="placeul">
                            <li><a href="<?php echo U('Index/start','','');?>">首页</a></li>
                            <li><a href="/myhome/admin.php?s=/Home/Goods">商品管理</a></li>
                            <li>添加商品</li>
                        </ul>
                    </div>
                    <form action="" method="post" name ="vform">
                        <input type ="hidden" name="goods_id" id="shopGoodsID" value="<?php echo ($info["goods_id"]); ?>">
                            <input type ="hidden" name="action" value="<?php echo ($data["action"]); ?>">
                                <input type="hidden" value="/myhome/admin.php?s=/Home/Goods/url_ajaxhinder" id="url_ajaxCalendar" name="url_ajaxCalendar" />
                                <input type ="hidden" name="admin" value=<?php echo ($_SESSION['admin']['name']); ?>>
                                    <div class="formbody">
                                        <div class="formtitle"><span><?php echo ($data["title"]); ?></span></div>
                                        <div id="detailDialog">
                                            <span style=" font-size: 16px; margin-top: -5px; ">商品参数</span>
                                            <table cellpadding="0" cellspacing="0" border="1" id="tableAdd">

                                            </table>
                                        </div>

                                        <ul class="forminfo">
                                            <li><label>商品名字</label><input name="goods_name" id="goods_name" type="text" class="dfinput" value="<?php echo ($info["goods_name"]); ?>" /><i id="name_info">名称不能超过128个字符</i></li>

                                            <!--    <li><label>商家名称</label>
                                                    <span class = 'pro'>
                                                        <select name ='store_id' id="type_n" class="dfinputInfo" >
                                                            <option class="pro_store"  value="<?php echo ($info["store_id"]); ?>"><?php echo ($info["store_name"]); ?></option>
                                                            <?php if(is_array($vip)): $i = 0; $__LIST__ = $vip;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class = "pro_into" value="<?php echo ($vo["store_id"]); ?>" name="store_id" ><?php echo ($vo["store_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    </span>
    
                                                    <i id="name_info">名称不能超过30个字符</i></li>  

                                            <li><label>商品型号</label><input name="marque" type="text" id="marque" class="dfinput"  value="<?php echo ($info["marque"]); ?>"/><i>商品型号</i></li>-->
                                            <li><label>价 格</label><input name="price" type="text" id="price" class="dfinput"  value="<?php echo ($info["price"]); ?>"/><i>商品价格</i></li>
                                            <li><label>库 存</label><input name="inventory" type="text" id="inventory" class="dfinput"  value="<?php echo ($info["inventory"]); ?>"/><i></i></li>


                                            <div style="display:none" id="skuNotice" class="sku_tip">
                                                <span class="validateTips"></span>
                                            </div>
                                            <i></i></li> 
                                            <input type="hidden" value="<?php echo ($info["list_img"]); ?>" id="img_list">
                                                <!--  <input type="hidden" value="<?php echo ($info["if_show"]); ?>" id="if_show"> -->
<!--                                                <li style="height:50px"><label>图文介绍</label></li>
                                                <li><textarea rows="5"  cols='40' style="" name ="intro" id="intro" value="<?php echo ($info["content"]); ?>" ><?php echo ($info["intro"]); ?></textarea></li>
                                                <li><label>自推自荐</label><textarea rows="5"  cols='50' style="border:1px solid #A7B5BC; width: 345px;" name ="sales" id="sales" value="<?php echo ($info["sales"]); ?>"><?php echo ($info["sales"]); ?></textarea><i>推荐。。</i></li>-->
                                                <!--    <li><label>是否上架</label><input name="if_show"  class="radiolist" id="rdaoid"  type="radio" value="1"><a class="box">是</a><input name="if_show"  class="radiolist" type="radio" value="0"><a class="box">否</a></li> -->
                                                <li style="height:85px"><label>商家描述</label><textarea rows="5"  cols='50' style="border:1px solid #A7B5BC; width: 345px;" id="description" name ="description" value="<?php echo ($info["description"]); ?>"><?php echo ($info["description"]); ?></textarea><i>描述</i></li>

                                                <input type ='hidden' name = "list_path" value="<?php echo ($info["list_path"]); ?>">
                                                    <input type ='hidden' name = "goods_img" value="<?php echo ($info["mid_pic"]); ?>">
                                                        <input type ='hidden' name = "list_img" value="<?php echo ($info["list_img"]); ?>">
                                                            <li><label>商品图册</label><i></i></li> 
                                                            <li style="position:relative;margin-bottom:5px;height:55px"><input type = "file" id ="upload_more">
                                                                    <i  id ="imgs_more" style="position:absolute;left:150px;top:-5px;">
                                                                        <div id = 'more_<?php echo ($k); ?>' class = ' more_list_pic' num = 0 style=" float: left"> 
                                                                        </div>
                                                                        <?php if($info["goods_img"] != ''): if(is_array($info["goods_img"])): $k = 0; $__LIST__ = $info["goods_img"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div id = 'more_<?php echo ($k); ?>' class = ' more_list_pic' num = "<?php echo ($k); ?>" style=" float: left">
                                                                                    <img width='50px' src='<?php echo ($vo["mid"]); ?>' name ='path' style='margin-left: 8px;'>
                                                                                        <input type='hidden' name='path[]'  value='<?php echo ($vo["pic"]); ?>'/>
                                                                                        <input type='hidden' name='mid[]' value='<?php echo ($vo["mid"]); ?>'/>
                                                                                        <img src='/myhome/App/Home/View/Public/Images/uploadify-cancel.png' class ='close' style='float: right;margin-left: 4px; ' onclick = "javascript:deletePic('<?php echo ($k); ?>')" > 
                                                                                        <input type='text' name='pic_name[]'class='form-control' style='width: 150px;float: right;margin-left: 5px;' value='<?php echo ($vo["name"]); ?>'/>
                                                                                       </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                                                    </i>
                                                            </li>

                                                                                          </ul>
                                  <input name="" type="submit" class="btn btn-primary" value="确认<?php echo ($data["btn"]); ?>"  onclick="javascript:;" style=" margin-top: 5px; float: left;" />
                                                                                    
                                                                                            </div>
                                                                                            </form>

                                                                                            </body>
                                                                                            <script>
                                                                                       //         var edit= new UE.ui.Editor({initialContent:'',initialFrameWidth:600});
//                                                                                                edit.render("intro");
                                                                                                var list_img = "";

                                                                                                var img = '';
                                                                                                var num = $('.more_list_pic').last().attr('num');
                                                                                                var path='';
                                                                                                  //  alert(num)
                                                                                                $('#upload_more').uploadify({
                                                                                                    'swf'      : '/myhome/App/Home/View/Public/Images/uploadify.swf',
                                                                                                    'uploader' : '<?php echo U("Uploads/photo","","");?>',
                                                                                                    'cancelImage':'/myhome/App/Home/View/Public/Images/uploadify-cancel.png',
                                                                                                    'buttonText' : '缩略图上传',
                                                                                                    'onUploadSuccess' : function(file, v, response) {
                                                                                                        // alert(data);
                                                                                                     //   console.log(data)
                                                                                                        obj= $.parseJSON(v);
                                                                                                         console.log(obj)
                                                                                                        img += "<div id = 'more_"+num+"' class = ' more_list_pic' num = '"+num+"' style='float: left'>"
                                                                                                        img += "<img width='50px' src='"+obj.path+"' name ='path' style='margin-left: 8px;' >";
                                                                                                        img += "<input type='hidden' name='path[]'  value='"+obj.path+"'/>";
                                                                                                        img += "<input type='hidden' name='mid[]' value='"+obj.mid+"'/>";//suolie
                                                                                             
                                                                                                        img += "<img src='/myhome/App/Home/View/Public/Images/uploadify-cancel.png' class ='close'style='float: right;margin-left: 4px; ' onclick = 'javascript:deletePic("+num+",\""+obj.list+"\",\""+obj.suolie+"\")'> ";
                                                                                                        img += "<input type='text' class='form-control'style='width: 150px;float: right;margin-left: 5px;' name='pic_name[]' value='"+obj.name+"'/>";
                                                                                                        img += "</div>"
                                                                                                        $('#imgs_more').append(img);
                                                                                                        img = '';
                                                                                                        num++;
                                                                                                    }
                                                                                                });
                                                                                                function deletePic(num,path,suolie){
                                                                                                //    alert(path)
                                                                                             //       console.log($("#more_"+num+"").html())
                                                                                                   // $("#more_"+num+"").remove();
                                                                                                    
                                                                                                    $.ajax({ 
                                                                                                        url:'<?php echo U("Uploads/imgDelete","","");?>',
                                                                                                        type:"post",
                                                                                                        dataType:"json",
                                                                                                        cache:false,
                                                                                                        data: {
                                                                                                            "path":path,
                                                                                                            "suolie":suolie
                                                                                                        },
                                                                                                        timeout:30000,
                                                                                                        error:function(data, msg){
                                                                                                            alert("error:"+msg);
                                                                                                        },
                                                                                                        success:function(data){
                                                                                                             if(data==1){
                                                                                                                 $("#more_"+num+"").remove(); 
                                                                                                             }else{
                                                                                                                 alert(删除失败)
                                                                                                             }
                                                                                                            console.log(data)
                                                                                                        }
                                                                                                    });     
                                                                                                    // $('this').parent('.more_list_pic').remove();
                                                                                                }
                                                                                                /*      function deleteListPic(){
                                                                                                    $(".up_list_pic").html('');
                                                                                                    $('#list_hidden').html('');
                                                                                                }*/
                                                                                                function deleteListPic(){
                                                                                                                                   
                                                                                                    $("#imgs img").remove();
                                                                                                    $('#list_hidde input').remove();
                                                                                                }

                                                                                            </script>
                                                                                            <script type="Text/Javascript">

                                                                                                                                                
                                                                                            </script>

                                                                                            </html>