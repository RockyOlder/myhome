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
<script type="text/javascript" src="/myhome/App/Home/View/Public/Js/bootstrap.min.js"></script>

<script type="text/javascript" src="/myhome/App/Home/View/Public/Js/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<link href="/myhome/App/Home/View/Public/Css/topShow.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/myhome/App/Home/View/Public/Css/bootstrap.min.css">
<script type="text/javascript" type="text/javascript">

   
         
         
    $(function(){
        $('#select_deve img').bind('click',function(){
            $("#vform").submit();
            //     selectDeveloper()
        });
    })   
</script>



</head>
<style type="text/css">
    form ul{width: 100%;}
    form ul li{float: left;width: 110px;line-height: 25px;text-align: center;}
    form ul input{border: 1px solid #ccc;width: 100px;}
    form ul select{border: 1px solid #ccc;width: 100px;}
</style>

    <body style="background: none;">
        <input type="hidden" value="/myhome/admin.php?s=/Home/Role/del/id/" id="url_ajaxCalendar" name="url_ajaxCalendar" />
        <div class="place">
            <span>位置： </span>
            
            <ul class="placeul">
                <li><a href="<?php echo U('Index/start');?>">首页</a></li>
                <li>系统管理</li>
                <li>角色列表</li>
            </ul>
        </div>

        <div class="rightinfo">

            <div class="tools">
                <div class="tools">
                    <form action="" method="post" name="vform" id="vform">
                        <ul>
                            <!-- <li>开发商类别:</li>
                            <li>
                                <select name="type">
                                    <option value="0">全部</option>
                                    <option value="1">总部</option>
                                </select>
                            </li> -->
                            <li>角色名称:</li>
                            <li><input type="text" value="" class="form-control" name ="name" /></li>
                            <li><span id="select_deve" style="cursor:pointer"><img src="/myhome/App/Home/View/Public/Images/ico06.png" width="25"/></span></li>
                            <a href="<?php echo U('add');?>"><li class="click"><span><img src="/myhome/App/Home/View/Public/Images/t01.png" /></span>添加角色</li></a>
                        </ul>
                    </form>

                </div>
            </div>


            <table class="tablelist">
                <thead>
                    <tr>
                        <!-- <th><input name="" type="checkbox" value="" checked="checked"/></th> -->
                        <th>编号<i class="sort"><img src="/myhome/App/Home/View/Public/Images/px.gif" /></i></th>
                        <th>角色名</th>
                        <th>描述</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>

                <tbody id="table_ajax_list">
                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                            <!-- <td><input name="num" type="checkbox" value="" /></td> -->
                            <td><?php echo ($vo["id"]); ?></td>
                            <td><?php echo ($vo["title"]); ?></td>
                            <td><?php echo ($vo["description"]); ?></td>  
                            <td><?php echo ($vo["status"]); ?></td>     
                            <td>
                                <a href="<?php echo U('authority',array(id=>$vo['id']),'');?>" class="tablelink"> 权限设置</a>
                                <a href="<?php echo U('edit',array(id=>$vo['id']),'');?>" class="tablelink">修改</a>    

                                <a href="<?php echo U('del',array(id=>$vo['id']),'');?>" class="tablelink" onclick="if(confirm('确认删除')){return true}else{return false}"> 删除</a>
                                

                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>    
                </tbody>
            </table>

            <input type="hidden" value="<?php echo U('del',array(id=>''),'');?>" id="del_url">
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