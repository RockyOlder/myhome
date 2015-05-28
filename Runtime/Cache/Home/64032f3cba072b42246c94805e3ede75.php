<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>无标题文档</title>
        <link href="/myhome/App/Home/View/Public/Css/style.css" rel="stylesheet" type="text/css" />
        <link href="/myhome/App/Home/View/Public/Css/tableList.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Js/jquery-ui/css/pepper-grinder/jquery-ui.min.css">
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/common.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/artDialog.js"></script>
            <link id="artDialogSkin" href="/myhome/App/Home/View/Public/Css/skin/aero/aero.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Css/bootstrap.min.css">
                <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
                <link href="/myhome/App/Home/View/Public/Css/topShow.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" type="text/javascript">
                    $(function(){
                        $( "#dialog-form" ).dialog({
                            width: 500,
                            autoOpen: false,
                            modal:true,
                            position:{
                                my: "center", 
                                at: "center", 
                                of:  window
                            }, 
                            buttons: {
                                "会员信息":function(){

                                }	
                            },
                            close: function() {
                            resetInput();
                            }
                        });
                        $( document ).tooltip({
                            track: false,
                            width: "100px",
                            position: {
                                my: "left+5 bottom-5", 
                                at: "center top"
                            }
                        });
                        
                        $('#select_deve img').bind('click',function(){
                            $("#vform").submit();
                            //     selectDeveloper()
                        });
                        initPager();
                        
                    })
                      var roleDataBak='{}';
                      var allFields=$( [] );
                    function update_list(subId){
                        $("button[title=close]").attr({ title: "关 闭"})
                   
                        $("#dialog-form").dialog("option","title","会员详情");
                        $.ajax({ 
                            url:$("#UserObjectFind").val(),
                            type:"post",
                            dataType:"json",
                            cache:false,
                            data: {
                                "uid":subId
                            },
                            timeout:30000,
                            error:function(data, msg){
                                alert("error:"+msg);
                            },
                            success:function(data){
                              console.log(data)
                                $("#user_name").text(data.user_name);
                                $("#srcImg").attr({ src: data.face});
                                $("#fax_phone").text(data.fax_phone);
                                $("#reg_time").text(data.reg_time);
                                $("#email").text(data.email);
                                $("#totle").text(data.totle);
                                $("#user_rank").text(data.user_rank);
                                $("#true_name").text(data.true_name);
                             //   $("#address").text(data.address);
                                $("#nickname").text(data.nickname);      
                                $("#source").text(data.source);
                                $("#user_money").text(data.user_money);    
                              if(data.pro!=null){$("#property").text(data.pro.pname);}
                              if(data.vill!=null){ $("#village").text(data.vill.name);}
                                $("#dialog-form").dialog("open");  

                            }
                        });
                    }
                      
                    /*           function cats_Shop(id) {
       
                        art.dialog({
                            content:'你确定要删除？',
                            title: '确定框',  
                            okValue:'确认',  
                            cancelValue:'取消', 
                            width: 230,  
                            height: 100,  
                            fixed:true,
                            id:'bnt4_test',
                            style:'confirm'}, 
                        function(){
                            var msg = art.dialog({id:'bnt4_test'}).data.content; // 使用内置接口获取消息容器对象
                            if(msg){
                                location.href=$("#url_ajaxCalendar").val()+id
                                return false;
                            }        
                        },function(){
                            return true;
                        });
                    };*/
                function resetInput(){
                    if($("#role_id").val()==""){
                        $("#dialog-edit input:text,#dialog-form input:hidden,#dialog-form textarea,#dialog-form span").each(function(){
                            $(this).val("");	$(this).text("");	
                        });
  
                        allFields.val("").removeClass("ui-state-error");
                        $(".validateTips").removeClass("errorTip").hide();
                    }
                }

                </script>


                </head>
                <style type="text/css">
                    form ul{width: 100%;}
                    form ul li{float: left;width: 110px;line-height: 25px;text-align: center;}
                    form ul input{border: 1px solid #ccc;width: 100px;}
                    form ul select{border: 1px solid #ccc;width: 100px;}
                    .th_default{padding: 0 3px;}
                    .redclss{ color: red;}
                    #ig_primary{float: right; margin-top: 3px;}
                    .divBtn {position:relative;display:inline-block;padding:3px;cursor:pointer}
                    .tablelist td{line-height:35px; text-indent: 10px; border-right: dotted 1px #c7c7c7;}
                    #table_list tr td{ padding: 7px;}
                </style>

                <body style="background: none;">
                    <input type="hidden" value="/myhome/admin.php?s=/Home/User/del/id/" id="url_ajaxCalendar" name="url_ajaxCalendar" />
                    <input type="hidden" value="/myhome/admin.php?s=/Home/User/order" id="examUpdate"  />
                    <input type="hidden" value="/myhome/admin.php?s=/Home/User/index" id="UserObjectFind" />
                    <div class="place">
                        <span>位置：</span>
                        <ul class="placeul">
                   
                            <li><a href="<?php echo U('Index/start','','');?>">首页</a></li>
                            <li>会员列表</li>
                        </ul>
                    </div>

                    <div class="rightinfo">
                        <div class="tools">
                            <form action="" method="post" name="vform" id="vform">
                                <ul>
                                    <li>会员用户名:</li>
                                    <li><input type="text" value="" class="form-control" name ="user_name" /></li>
                                    <li><span id="select_deve"><img src="/myhome/App/Home/View/Public/Images/ico06.png" width="25"/></span></li>
                                    <!-- <li></li> -->
                                </ul>
                            </form>

                        </div>
                        <table class="tablelist">
                            <thead>
                                <tr>
                                    <th>编号<i class="sort"><img src="/myhome/App/Home/View/Public/Images/px.gif" /></i></th>
                                    <th>用户名</th>
                                    <th>真实姓名</th>
                                    <th>手机号码</th>
                                    <th>邮箱</th>
                                    <th>会员级别</th>
                                    <th>地址</th>
                                    <th>注册时间</th>
                                    <th width="35px">操作</th>
                                </tr>
                            </thead>

                            <tbody id="table_ajax_list">
                                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>

                                        <td><?php echo ($vo["user_id"]); ?></td>
                                        <td><?php echo ($vo["user_name"]); ?></td>
                                        <td><?php echo ($vo["mobile_phone"]); ?></td>
                                        <td><?php echo ($vo["fax_phone"]); ?></td>
                                        <td><?php echo ($vo["email"]); ?></td>
                                        <td><?php echo ($vo["user_rank"]); ?></td>
                                        <td><?php echo ($vo["address"]); ?></td>                                    
                                        <td><?php echo (date("Y-m-d H:i:s",$vo["reg_time"])); ?></td>


                                        <!--<td>
                                            <a href="<?php echo U('add',array(id=>$vo['user_id']),'');?>" class="tablelink">修改</a>    
                                        <!--  href="<?php echo U('del',array(id=>$vo['user_id']),'');?>" 
                                         <a class="tablelink" onclick="return cats_Shop(<?php echo ($vo["user_id"]); ?>)"> 删除</a>
                                         <a href="<?php echo U('updatePW',array(id=>$vo['user_id']),'');?>" class="tablelink">修改密码</a>  
                                     </td> -->
                                        <td width="20px" class="th_default" align="center"><div  class="divBtn editBtn ui-state-default ui-corner-all" title="查看详细" onclick="update_list(<?php echo ($vo["user_id"]); ?>)" ><span class="ui-icon ui-icon-add"></span></div></td>
                                        <!--   <td width="20px" class="th_default" align="center"><a href="<?php echo U('add',array(id=>$vo['user_id']),'');?>" class="divBtn editBtn ui-state-default ui-corner-all" title="编辑" ><span class="ui-icon ui-icon-pencil"></span></a></td>
                                           <td width="20px" class="th_default" align="center"><div class="divBtn deleteBtn ui-state-default ui-corner-all" title="删除"onclick="return cats_Shop(<?php echo ($vo["user_id"]); ?>)"><span class="ui-icon ui-icon-minus"></span></div></td>
                                           <td width="20px" class="th_default" align="center"><a href="<?php echo U('updatePW',array(id=>$vo['user_id']),'');?>" class="divBtn deleteBtn ui-state-default ui-corner-all" title="修改密码"><span class="ui-icon ui-icon-role"></span></a></td>-->
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>    
                            </tbody>
                        </table>
                        <div id="pager" class="pager">
                            <div class="fanye">
                                <div class="fanye1">
                                    <?php echo ($page); ?>
                                </div>
                                <div class="fanye2">
                                    <span class="">共<?php echo ($currentPage); ?>/<?php echo ($totalPage); ?>页</span>
                                    转到<input type="text" value="<?php echo ($currentPage); ?>" id="gopage_input" class="ui-widget-header_page" />页&nbsp;
                                    <input type="button" value="确定" id="gopage_btn_confirm" />
                                </div>
                            </div>
                        </div>
                        <div id="dialog-form" title="问题提交" style=" display: none;">
                            <form action="#" method="post" name="myname" class="form-input" />
                            <input type ="hidden" name="action" id="action2" value="edit" >
                                <fieldset>
                                    <table id="table_list" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td align="right" width="90px">
                                                <label for="number">用户名：</label>
                                            </td>
                                            <td>
                                                <span id="user_name" ></span>
                                            </td>
                                            <td rowspan="3" style="text-align:right">
                                                <img id="srcImg" src="" width="100px" height="110px"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="90px">
                                                <label for="nickname">昵称：</label>
                                            </td>
                                            <td>
                                                <span id="nickname" ></span>
                                            </td>
                                        </tr>
                                        <tr>    
                                            <td align="right" width="90px">
                                                <label for="totle">会员等级：</label>
                                            </td>
                                            <td>
                                                <span id="user_rank"  /> </span>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right" width="90px">
                                                <label for="ordername">真实姓名：</label>
                                            </td>
                                            <td>
                                                <span id="true_name"  /></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="bid">固定电话：</label>
                                            </td>
                                            <td>
                                                <span id="fax_phone" ></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="statue">邮箱：</label>
                                            </td>
                                            <td>
                                                <span id="email" ></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="statue">所属物业：</label>
                                            </td>
                                            <td>
                                                <span id="property" ></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="statue">所属小区：</label>
                                            </td>
                                            <td>
                                                <span id="village" ></span>
                                            </td>
                                        </tr>
                                      <!--  <tr>
                                            <td align="right">
                                                <label for="address">居住地址：</label>
                                            </td>
                                            <td>
                                                <span id="address" ></span>
                                            </td>
                                        </tr>-->


                                        <tr>
                                            <td align="right">
                                                <label for="source">用户积分：</label>
                                            </td>
                                            <td>
                                                <span id="source" ></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="user_money">用户余额：</label>
                                            </td>
                                            <td>
                                                <span id="user_money" ></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                <label for="time">添加时间：</label>
                                            </td>
                                            <td>
                                                <span id="reg_time" ></span>
                                            </td>
                                        </tr>


                                        <!--       <tr>    
                <tr>    
                             <td align="right" width="90px">
                                 <label for="express">配送方式：</label>
                             </td>
                             <td>
                                 <span id="express"  /> </span>
                             </td>
                         </tr>
                             <td align="right" width="90px">
                                 <label for="cate">订单分类：</label>
                             </td>
                             <td>
                                 <span id="cate"  /> </span>
                             </td>
                         </tr>
                         <tr>    
                             <td align="right" width="90px">
                                 <label for="express_number">快递编号：</label>
                             </td>
                             <td>
                                 <span id="express_number"  /> </span>
                             </td>
                         </tr>
                       <tr>
                              <td align="right" width="90px">
                                  <label for="title">提交审核：</label>
                              </td>
                              <td>
                                  <select name = 'done' id="type_on" class="form-control">
                                      <option class = "top_cate">请选择</option>
                                      <option class = "top_cate"  value="0">未通过审核</option>
                                      <option class = "top_cate" value="1" >已审核</option>
                                  </select>
                              </td>
                          </tr>  -->
                                        <input type="hidden" name="oid" id="role_id"  />
                                    </table>
                                </fieldset>
                                </form>
                        </div>



                    </div>

                    <script type="text/javascript">
                        $('.tablelist tbody tr:odd').addClass('odd');
                    </script>

                </body>

                </html>