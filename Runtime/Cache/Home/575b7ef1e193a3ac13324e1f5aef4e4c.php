<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>无标题文档</title>
        <link href="/myhome/App/Home/View/Public/Css/style.css" rel="stylesheet" type="text/css" />
        <link href="/myhome/App/Home/View/Public/Css/tableList.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Js/jquery-ui/css/pepper-grinder/jquery-ui.min.css">
            <link id="artDialogSkin" href="/myhome/App/Home/View/Public/Css/skin/aero/aero.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/common.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/bootstrap.min.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/artDialog.js"></script>
            <script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
            <link href="/myhome/App/Home/View/Public/Css/topShow.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Css/bootstrap.min.css">
                <script type="text/javascript" type="text/javascript">
                    function deleteSum(id){
                        if(confirm("确认删除"))
                            location.href="/whr/index.php?s=/Home/Admin/del/id/"+id
                    }
                    $(function(){
                          $( document ).tooltip({
                            track: true,
                            width: "100px",
                            position: {
                                my: "left+5 bottom-5", 
                                at: "center top"
                            }
                        });
                        initPager();
                    })
                    function cats_Shop(id) {
                    
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
                    };
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
                </style>

                <body style="background: none;">
                  <input type="hidden" value="/myhome/admin.php?s=/Home/Category/del/id/" id="url_ajaxCalendar" name="url_ajaxCalendar" />
                    <div class="place">
                     <span>位置： </span>
                        <ul class="placeul">
                               <li><a href="<?php echo U('Index/start','','');?>">首页</a></li>
                            <li>商品管理</li>
                            <li>分类列表</li>
                        </ul>
                    </div>

                    <div class="rightinfo">

                        <div class="tools">

                        </div>


                        <table class="tablelist">
                            <thead>
                                <tr>

                                    <th>编号<i class="sort"><img src="/myhome/App/Home/View/Public/Images/px.gif" /></i></th>
                                    <th>分类级别</th>
                                    <th>名称</th>
                                    <th>描述</th>
                              
                                    <th colspan="2">操作</th>
                                </tr>
                            </thead>

                            <tbody id="table_ajax_list">
                                <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>

                                        <td><?php echo ($vo["cat_id"]); ?></td>
                                        <?php if($vo["lev"] == '0' ): ?><td><span>一级分类</span></td><?php endif; ?>
                                        <?php if($vo["lev"] > '0' ): ?><td ><span>二级分类</span></td><?php endif; ?>
                                        <td>  
                                            <?php if($vo["parent_id"] == '0' ): ?><img src="/myhome/App/Home/View/Public/img/menu_minus.gif" id="icon_0_1" style="margin-left:<?php echo ($vo["lev"]); ?>px" /><?php echo ($vo["cat_name"]); ?></img><?php endif; ?> 
                                            <?php if($vo["parent_id"] != '0' ): echo ($vo["cat_name"]); endif; ?> 
                                        </td>
                                        <td width="350px;"><?php echo ($vo["intro"]); ?></td>
                                     <!--   <td><img height='50px' src="<?php echo ($vo["cat_img"]); ?>"  /></td> -->
                      
                                        <td width="20px" class="th_default" align="center"><a href="<?php echo U('add',array(id=>$vo['cat_id']),'');?>" class="divBtn editBtn ui-state-default ui-corner-all" title="编辑" ><span class="ui-icon ui-icon-pencil"></span></a></td>
                                        <td width="20px" class="th_default" align="center"><div class="divBtn deleteBtn ui-state-default ui-corner-all" title="删除"onclick="return cats_Shop(<?php echo ($vo["cat_id"]); ?>)"><span class="ui-icon ui-icon-minus"></span></div></td>

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
                                    转到<input type="text" value="<?php echo ($currentPage); ?>" id="gopage_input" class="ui-widget-header" />页&nbsp;
                                    <input type="button" value="确定" id="gopage_btn_confirm" />
                                </div>
                            </div>
                        </div>


                        <div class="tip">
                            <div class="tiptop"><span>提示信息</span><a></a></div>

                            <div class="tipinfo">
                                <span><img src="/myhome/App/Home/View/Public/Images/ticon.png" /></span>
                                <div class="tipright">
                                    <p>是否确认对信息的修改 ？</p>
                                    <cite>如果是请点击确定按钮 ，否则请点取消。</cite>
                                </div>
                            </div>

                            <div class="tipbtn">
                                <input name="" type="button"  class="sure" value="确定" />&nbsp;
                                <input name="" type="button"  class="cancel" value="取消" />
                            </div>

                        </div>




                    </div>

                    <script type="text/javascript">
                        $('.tablelist tbody tr:odd').addClass('odd');
                    </script>

                </body>

                </html>