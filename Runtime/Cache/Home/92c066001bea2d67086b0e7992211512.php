<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>添加开发商</title>
        <link href="/myhome/App/Home/View/Public/Css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Js/jquery-ui/css/pepper-grinder/jquery-ui.min.css">
            <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Css/bootstrap.min.css">
                <link rel="stylesheet" href="/myhome/App/Home/View/Public/Css/uploadify.css">
                    <!-- <link href="/myhome/App/Home/View/Public/Css/select.css" rel="stylesheet" type="text/css" /> -->
                    <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery.js"></script>
                    <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/common.js"></script>
                    <script type="text/javascript" src='/myhome/App/Home/View/Public/Js/jquery.uploadify.min.js'></script>
                    <!-- <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery.idTabs.min.js"></script> -->
                    <!-- <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/select-ui.min.js"></script> -->
                    <!-- <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/kindeditor.js"></script> -->

                    <script type="text/javascript">
                        $(function(){
                            $('form').submit(function(){
                                if(!checkInput()){
                                    $('.dfinput').each(function () {
                                        if($(this).val()==''){
                                            $(this).next().css("color","red");
                                            $('.errorColor').css("color","red")
                                        }
                                    });
                                    return false;
                                }
                                return true
                            })
                            $('#village_id').bind('change',function(){
                                $(this).parent().next().css("color","#7f7f7f")
                            })
                            $(".dfinput").bind("focus",function(){
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
                            if($("#info_ding").val()==''){ 
                                $("#info_ding").val("0")
                            }
                            var img = "";
                            //    var span="";
                            $('#upload_list').uploadify({
                                'swf'      : '/myhome/App/Home/View/Public/Images/uploadify.swf',
                                'uploader' : '<?php echo U("Uploads/listUpload");?>',
                                'cancelImage':'/myhome/App/Home/View/Public/Images/uploadify-cancel.png',
                                'buttonText' : '列表上传',
                                'multi': false,
                                'onUploadSuccess' : function(file, data, response) {
                                    obj= $.parseJSON(data);
                                    img += "<img height='50px' src='"+obj.path+"'>";
                                    img +=" <img src='/myhome/App/Home/View/Public/Images/uploadify-cancel.png' class ='close' onclick = 'javascript:deleteListPic()'>"
                                    $('#imgs').html(img);
                                    var hid ="<input name='thumb_pic' type='hidden' value='"+obj.mid+"' />"
                                    $('#list_hidden').html(hid);
                                    img = '';
                                    hid='';
                                }
                            });
                        })
                        function deleteListPic(){
                                                                                                                                   
                            $("#imgs img").remove();
                            $('#list_hidde input').remove();
                        }
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
                            bValid = bValid && checkLength( $("#name"), "分类名称", 2, 16 );
                            bValid = bValid && checkEmpty( $("#intro"), "描述不能为空！" );
                            $("#intro").removeClass('ui-state-error')
                            if(bValid==false){ setout(); }
                            return bValid;
                        }
                    </script>
                    </head>
                    <style type="text/css">
                        .sku_tip { background: none repeat scroll 0 0 rgba(0, 0, 0, 0.7);border-radius: 4px;box-shadow: 0 0 3px 3px rgba(150, 150, 150, 0.7);color: #fff;display: none;left: 50%;margin-left: -70px; padding: 5px 10px;position: fixed; text-align: center; top: 50%;z-index: 25;}
                        .pro select{width: 345px;height: 32px; }
                        .cat_img{margin-top: -40px;} 
                        .pro span{width: 345px;height: 32px; }
                        #spanDes{ font-size: 13px; color: #7f7f7f; float: left; margin:0 300px; margin-top: -30px;}
                        #pro_category{float: left; margin: 0 345px; margin-top: -25px;}
                        /*  .top_cate{background-image: url(/myhome/App/Home/View/Public/img/menu_minus.gif); width:10px; height:10px;} */
                    </style>

                    <body style="background: none;">

                        <div class="place">
                            <span>位置： </span>
                            <ul class="placeul">
                                <li><a href="<?php echo U('Index/start','','');?>">首页</a></li>
                                <li><a href="/myhome/admin.php?s=/Home/Category">分类管理</a></li>
                                <li>添加分类</li>
                            </ul>
                        </div>
                        <form action="" method="post" name ="vform">
                            <input type ="hidden" name="cat_id" value="<?php echo ($info["cat_id"]); ?>">
                                <input type ="hidden" name="action" value="<?php echo ($data["action"]); ?>">
                                    <input type ="hidden" name="admin" value=<?php echo ($_SESSION['admin']['name']); ?>>
                                        <div class="formbody">
                                            <div class="formtitle"><span><?php echo ($data["title"]); ?></span></div>

                                            <ul class="forminfo">
                                                <li><label>分类名称</label><input name="cat_name" id="name" type="text" class="dfinput" value="<?php echo ($info["cat_name"]); ?>" /><i id="name_info">名称不能超过30个字符</i></li>
                                                <li><label>上级分类</label>
                                                    <span class = 'pro'>
                                                        <select name = 'parent_id' id="type_on" class="form-control" >
                                                            <option class="pro_into" id="info_ding" value="0">顶级分类</option>
                                                            <?php if(is_array($arr)): $i = 0; $__LIST__ = $arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option class = "top_cate" value="<?php echo ($vo["cat_id"]); ?>" <?php echo ($vo["selected"]); ?>><span><?php echo ($vo["cat_name"]); ?></span></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                                        </select><i id="pro_category">顶级分类不用选择</i></span>
                                                </li>
                                                <!--<li><label>分类图片</label><div id="list_hidden"></div></li>
                                                <li id="limage" style="position:relative;margin-bottom:5px;height:55px"><input name="" id="upload_list" type="file" class="dfinput" style=""/><i  id ="imgs" style="position:absolute;left:150px;top:-5px;"><img src="" style="" height="50px">
                                                            <?php if($info["cat_img"] != ''): ?><div class="up_list_pic">
                                                                    <img class="cat_img" height='50px' src='<?php echo ($info["cat_img"]); ?>'>
                                                                        <img class="cat_img" src='/myhome/App/Home/View/Public/Images/uploadify-cancel.png' class ='close' onclick = 'javascript:deleteListPic()'> 
                                                                            </div><?php endif; ?>
                                                                            </i><span id="spanDes">顶级分类无图片</span></li> -->
                                                                            <li><label>描述</label><textarea rows="7"  cols='56' style="border:1px solid #A7B5BC; width: 345px;" name ="intro" class="form-control" id="intro" value="<?php echo ($info["intro"]); ?>"><?php echo ($info["intro"]); ?></textarea></li>
                                                                            <li><label>&nbsp;</label><input type="submit" class="btn btn-primary" value="确认<?php echo ($data["btn"]); ?>"  onclick="javascript:;" /></li>
                                                                            </ul>
                                                                            <div style="display:none" id="skuNotice" class="sku_tip">
                                                                                <span class="validateTips"></span>
                                                                            </div>
                                                                            </div>
                                                                            </form>

                                                                            </body>

                                                                            </html>