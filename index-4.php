<?php
define('ACC', true);
require ('./include/init.php');
$goods = new GoodsModel();
$goodsFind = $goods->find(61);
$goodsFind['goods_img'] = json_decode($goodsFind['goods_img'], true);

//print_r($goodsFind['goods_img']);
////
//exit;
?>

<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>My photo </title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
        <meta name="description" content="Your description">
        <meta name="keywords" content="Your keywords">
        <meta name="author" content="Your name">
        <link rel="stylesheet" href="css/bootstrap.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
        <link rel="stylesheet" href="css/preview.css" type="text/css">
        <script type="text/javascript" src="js/include_script.js"></script>
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <link href="css/ie.css" rel="stylesheet" type="text/css">
    <![endif]-->
    </head>
    <body>
        <!--content wrapper-->
        <div id="wrapper">
            <section>
                <div class="container">
                    <div class="dynamicContent">
                        <!--content-->
                        <div class="row">

                            <div class="span11">
                                <h2> </h2>
                                <?php foreach($goodsFind['goods_img'] as $g) { ?>
                                <div class="wrapper img_col1">
                                    <a class="fancybox"  href="<?php  echo $g['pic']; ?>" rel="index">
                                        <img src="<?php  echo $g['mid']; ?>" alt="" style=" height: 100px;" class="img-polaroid"><span class="zoom animate"><span class="txt"><?php  echo $g['name']; ?></span></span>
                                    </a>
                                    <p><a href="#"><?php  echo $g['name']; ?></a></p>
                                </div>
                                 <?php } ?>



                            </div>





                        </div>
                    </div>
                </div>
            </section>
        </div>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script>
            $(".fancybox").fancybox({	
                //                				openEffect  : 'none',
                //				closeEffect : 'none',
                //
                //				prevEffect : 'none',
                //				nextEffect : 'none',
                //
                //				closeBtn  : false,
                //
                //				helpers : {
                //					title : {
                //						type : 'inside'
                //					},
                //					buttons	: {}
                //				},
                //				afterLoad : function() {
                //				//	this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
                //				}
            });
        </script>
    </body>
</html>