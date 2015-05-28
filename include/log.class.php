<?php
/*
 * 记录信息到日志
 * 在1次进程中，filesize的结果会被缓存(很多文件信息获取函数的结果都会被缓存，如filesiez)
 * 因此第一次运行时，读到的size全是0(即使这个过程中文件的内容已经被修改)
 * 当我们再次刷新时:filesize >1 m
 * curr.log-->113213.bak
 * 新建curr.log 0KB
 * 
 * 在上述问题中，问题出现在哪？
 * 答：即便curr.log->rename->112053465.bak了
 * 但，feilesiez 还是缓存了
 * 因此 。循环的5000次中，始终认为curr.log>1M
 * 始终备份......
 * 
 * 思路:
 * 给定内容，写入文件(fopen,fwrite)
 * 如果文件大于>1M 重写写一份
 * 
 * 传给我一个内容
 * 判断当前日志的 大小
 * 如果>1M,备份
 * 否则，写入
 */
class log{
	//写日志
	const LOGFILE = 'curr.log';//建一个常量，代表日志文件的名称
	public static function write($cont){
		$cont.= "\r\n";
		//判断大小
		$log =self::isBak();//计算出日志文件的地址
		
		$fh=fopen($log,'ab');
		fwrite($fh,$cont);
	     fclose($fh);
	}
	//备份日志

	public static function bak(){
		//原来的日志文件，改个名，存储起来
		//改成 年-月-日.bak这种形式
		$log= ROOT.'data/log/'.self::LOGFILE;
		$bak=ROOT.'data/log/'. date('ymd'). mt_rand(10000,99999).'.bak';
		return rename($log,$bak);
		
	}
		//读取并判断日志的大小
	public static function isBak(){
		$log= ROOT .'data/log/'.self::LOGFILE;
		if(!file_exists($log)){//如果文件不存在，则创建该文件
			//touch— 设定文件的访问和修改时间,快速的建立一个文件
		   touch($log);
		   return $log;
		}
		//要是存在，怎判断大小
		//清除缓存
		clearstatcache(true,$log);
	   $size=filesize($log);
	   if ($size <=1024* 1024){
	   	//大于1M
	   	return $log;
	   }
	   //走到这一行说明>1M
	  if (!self::bak()){
	  	return $log;
	  }
	  else{
	  	touch($log);
	  	return $log;
	  }
	}
}










?>