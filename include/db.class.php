<?php
/*
 * 数据库类
 */
abstract class db{
	//连接服务器h服务器，U用户名 P 密码
	//public abstract function connect($h,$u,$p);
	//发送查询
		public abstract function query($sql);
		//查询多行数据
		public abstract function getAll($sql);
		//查询单行数据
			public abstract function getRow($sql);
		//查询单个数据
	public abstract function getOne($sql);
	//自动执行insert/updata语句
	/*
	 * $this->autoExecute('user',array('username'=>'zhansan','email'=>'zhang@163.com'),''insert);
	 * 将形成  insert into user (username,email) values ('zhangshan','zhang@163.com');
	 */
    public abstract function autoExecute($table,$data,$act='insert',$where='');

}