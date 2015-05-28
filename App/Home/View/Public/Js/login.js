function checklogin(){
	// alert(1);
	var name = $('input[name=username]').val();
	var password = $('input[name=password]').val();
	var url =$('input[name=url_go]').val();
	if($.trim(name) == ''){
		alert('请输入用户名');
	}else if($.trim(password) == ''){
		alert('请输入用户密码');
	}else{
		var data = $('form').serialize();
//		var url =$('#form_action').val();
		$.ajax({
			type:"post",
			data:data,
			dataTapy:'json',
			success:function(data){
				if(data.statue == 1){
					window.location.href='index.php?s=/Home/Index/index.html';
				}else if(data.statue == 2){
					window.location.href='life.php?s=/Home/Index/index.html';
				}else if(data.statue == 3){
					window.location.href='vip.php?s=/Home/Index/index.html';
				}else if(data.statue == 4){
					window.location.href='server.php?s=/Home/Index/index.html';
				}
				else{
					alert(data.msg);
					location.reload() 
					
				}
			},
			error:function(){
				alert('通讯有误请检查网络');
			}
		});
	}
}

 function changeVerify(){  
        var timenow = new Date().getTime();  
        document.getElementById('verifyImg').src='index.php?s=/Home/Login/verify&'+timenow;    
    }  