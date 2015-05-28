<?php

//require_once 'db.class.php';
class mysql extends db {

    private static $ins = NULL;
    private $conn = NULL;
    private $conf = array();
    public $arr = null;
    public $last_id = null;

    protected function __construct() {
        $this->conf = conf::getIns();

        $this->connect($this->conf->host, $this->conf->user, $this->conf->pwd);
        //  $this->select_db($this->conf->db);
        $this->setChar($this->conf->char);
    }

    public function __destruct() {
        
    }

    public static function getIns() {
        if (!(self::$ins instanceof self)) {
            self::$ins = new self();
        }

        return self::$ins;
    }

    public function connect($DB_DSN, $DB_USERNAME, $DB_PASSWORD) {
        //  $this->conn = mysql_connect($h,$u,$p);
        $this->conn = new PDO($DB_DSN, $DB_USERNAME, $DB_PASSWORD);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (!$this->conn) {
            $err = new Exception('连接失败');
            throw $err;
        }
    }

//    protected function select_db($db) {
//        $sql = 'use ' . $db;
//        $this->query($sql);
//    }

    protected function setChar($DB_CHARSET) {
        // $sql = 'set names ' . $char;
        return $this->conn->exec("SET NAMES " . $DB_CHARSET); //设置成统一编码//$this->query($sql);
    }

    public function query($sql, $arr = NULL) {
		$sqltype = strtolower(substr(trim($sql),0,6)); //得到sql语句类型
		switch($sqltype){
			//select 成功返回查询结果 ，二维数组
			case "select"://查询语句
				$statement=$this->conn->prepare($sql);//准备sql 语句
				$statement->execute($arr);//传参 限制  给占位符传参数
				//$statement->setFetchMode(PDO::FETCH_ASSOC);//查询得到的结果为关联数组
				$result=$statement->fetchAll();//得到结果集  数组
		        return $result;
			break;
			//insert update delete 返回影响记录条数	
			case "insert":	
			case "update":
			case "delete":
                          
				$statement=$this->conn->prepare($sql);//准备sql语句
				$statement->execute($arr);//传参
                               
                            //    $this->last_id = $this->conn->lastInsertId(); 返回最新插入的id
                            //   print_r($this->last_id);exit;
                            //    print_r($this->conn->execute());exit;
				return $this->arr=$statement->rowCount();//如果成功 返回记录数
				break;
				
			default :
				return false;
				break;
		}
        //
        //$statement->setFetchMode(PDO::FETCH_ASSOC);//查询得到的结果为关联数组
        //$result = $statement->fetchAll(); //得到结果集  数组
         
    }

//    public function execSQL($sql, $arr = NULL) {
//        $statement = $this->pdo->prepare($sql); //准备sql语句
//        $statement->execute($arr); //传参
//        return $statement->rowCount(); //如果成功 返回记录数
//    }

    public function autoExecute($table, $arr, $mode = 'insert', $where = ' where 1 limit 1') {
        /*    insert into tbname (username,passwd,email) values ('',) 
          /// 把所有的键名用','接起来
          // implode(',',array_keys($arr));
          // implode("','",array_values($arr));
         */

        if (!is_array($arr)) {
            return false;
        }
        //update 拼接  
        if ($mode == 'update') {
            $sql = 'update ' . $table . ' set ';
            foreach ($arr as $k => $v) {
                $sql .= $k . "='" . $v . "',";
            }
            $sql = rtrim($sql, ',');
            $sql .= $where;

            return $this->query($sql);
        }
        //insert 拼接  

        $sql = 'insert into ' . $table . ' (' . implode(',', array_keys($arr)) . ')';
        $sql .= ' values (\'';
        $sql .= implode("','", array_values($arr));
        $sql .= '\')';

        return $this->query($sql);
    }

    //取所在数据函数***************************************************************  


    public function getAll($sql) {
  //    print_r($this->arr);exit;
        $rs = $this->query($sql);
     //   print_r($rs);exit;
//        $list = array();
//        while ($row = $rs) {
//            $list[] = $row;
//        }

        return $rs;
    }

    public function getRow($sql) {
      
        $rs = $this->query($sql);
     //   print_r($rs[0]);exit;
        return $rs[0];
    }

    public function getOne($sql) {
    //    echo 1;exit;
        $row = $this->query($sql);
       // print_r($row[0][0]);exit;
   //     $row = mysql_fetch_row($rs);

        return $row[0][0];
    }

    // 返回影响行数的函数
    public function affected_rows() {
      // print_r($this->conn->rowCount());exit;
        
        return $this->arr;
    }

    // 返回最新的auto_increment列的自增长的值
    public function insert_id() {
        return $this->conn->lastInsertId();
    }

}