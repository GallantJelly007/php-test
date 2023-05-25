<?php

class Connect{
    public $connect;
    private $error;
  
    public function __construct() {
        $c = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        $c->set_charset("utf8");
        if(mysqli_connect_errno()){
            $this->connect=false;
        }else{
            $this->connect=$c;
        }
    }
    
    public function sendQuery(string $queryString,string $typeParam=null,array $param=null){
        $arrParam = array();
        if(!is_null($typeParam)&&!is_null($param)){
            $arrParam[]=&$typeParam;
            for($i=0;$i<count($param);$i++){
                $arrParam[]=&$param[$i];
            }
        }else{
            $arrParam=null;
        }
        try{
            $stmt = $this->connect->prepare($queryString);
            if(!$stmt){
                ['success'=>false];
            }
            if(!is_null($arrParam)){
                try{
                    $reflect = new ReflectionMethod('mysqli_stmt','bind_param');
                    $reflect->invokeArgs($stmt,$arrParam);
                }catch(Exception $e){
                    ['success'=>false];
                }
            }
            $error=$stmt->execute();
            if($error==false){
                $this->error=$stmt->error;
                ['success'=>false];
            }
            $result = $stmt->get_result();
 
            $result=['success'=>true,'data'=>$result];
            if(preg_match('~^INSERT~',$queryString)){
                $result=['success'=>true,'insertId'=>$stmt->insert_id];
            }
            $stmt->close();
            return $result;
        }catch(Exception $e){
            return ['success'=>false];
        }
    }
    
    public function error(){
        return $this->error;
    }
    
    public function dbError(){
        return mysqli_error($this->connect);
    }

    public function __destruct() {
        $this->connect->close();
    }
}



