

// Cloud Float...
    var $main = $cloud = mainwidth = null;
    var offset1 = 450;
	var offset2 = 0;
	
	var offsetbg = 0;
    
    $(document).ready(
        function () {
            $main = $("#mainBody");
			$body = $("body");
            $cloud1 = $("#cloud1");
			$cloud2 = $("#cloud2");
			
            mainwidth = $main.outerWidth();
         
        }
    );

    /// 椋樺姩
    setInterval(function flutter() {
        if (offset1 >= mainwidth) {
            offset1 =  -580;
        }

        if (offset2 >= mainwidth) {
			 offset2 =  -580;
        }
		
        offset1 += 1.1;
		offset2 += 1;
        $cloud1.css("background-position", offset1 + "px 100px")
		
		$cloud2.css("background-position", offset2 + "px 460px")
    }, 70);
	
	
	setInterval(function bg() {
        if (offsetbg >= mainwidth) {
            offsetbg =  -580;
        }

        offsetbg += 0.9;
        $body.css("background-position", -offsetbg + "px 0")
    }, 90 );
	
    // 登录界面的js判断
    $(function(){
        
        //简单验证
        var validate = {
            'username' : false,
            'password' : false
            // ,
            // 'verify' : false
        };
        $('#username').blur(function(){
            if($.trim($(this).val()) == ''){
                $('#username_info').text('用户名不能为空');
                validate.username = false;
            }else{
                $('#username_info').text('');
                validate.username = true;
            }
        });
        $('#password').blur(function(){
            if($.trim($(this).val()) == ''){
                $('#password_info').text('密码不能为空');
                validate.password = false;
            }else{
                $('#password_info').text('');
                validate.password = true;
            }
        });
        //验证码每敲键盘都检查，不能用blur事件，因为用blur的话，当点击提交按钮时可以得到异步来的结果，而用回车提交
        //的话，异步数据延迟，在提交之后获取到异步结果，导致即使是输入正确，validate.verify还是false，以致提交不了
        // $('#verify').keyup(function(){
        //     var code = $(this).val();
        //     // alert(code);
        //     if($.trim(code) == ''){
        //         $('#verify_info').text('验证码不能为空');
        //         validate.verify = false;
        //     }

        //     if(code.length == 4){
        //         // 如果验证码输入是四位，当失焦时发异步看输入是否正确
        //         $.ajax({
        //             url : "{:U('Login/check','','')}",
        //             type : "post",
        //             data : "code="+code,
        //             dataType : "json",
        //             success : function(data){
        //                 if(data){
        //                     validate.verify = true;
        //                     $('#verify_info').text(''); 
        //                 }else{
        //                     $('#verify_info').text('验证码输入错误');
        //                     validate.verify = false;
        //                 }
        //             }
        //         });
        //     }else{
        //         $('#verify_info').text('验证码位数错误');
        //         validate.verify = false;
        //     }
            
        // });
        // 当点击登录按钮时验证输入是否为空
        $('form').submit(function(){
            $('#username').trigger('blur');
            $('#password').trigger('blur');
            // $('#verify').trigger('keyup');
            var isOK = validate.username && validate.password;// && validate.verify
            if(!isOK){
                
                return false;
            }

                return true;
            
        });



    })