<?php


class Model{
   
   private $objects=null;
   protected static $table=null;
   protected static $primary=null;
   private $data=null;
   private $dataTypes=null;
   private $query=null;
   
   static function select($param=null,$distinct=false,$tb=null){
       $d="";
       if($distinct){
           $d="DISTINCT ";
       }
       if(is_null($tb)){
           $tb=static::$table;
       }
       if(!is_null($param)){
           $str = implode(',',$param);
           $query="SELECT ".$d.$str." FROM ".$tb;
       }else{
           $query="SELECT ".$d."* FROM ".$tb;
       }
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
   }
   
   function andSelect($param=null,$distinct=false,$tb=null){
       $d="";
       if($distinct){
           $d="DISTINCT ";
       }
       if(is_null($tb)){
           $tb=static::$table;
       }
       if(!is_null($param)){
           $str = implode(',',$param);
           $this->query.="SELECT ".$d.$str." FROM ".$tb;
       }else{
           $this->query.="SELECT ".$d."* FROM ".$tb;
       }
       return $this;
   }
   
   static function count($fieldName=null,$distinct=false){
       $d="";
       if($distinct){
           $d="DISTINCT ";
       }
       if(!is_null($fieldName)){
           $query="SELECT COUNT(".$d.$fieldName.") AS count FROM ".static::$table;
       }else{
           $query="SELECT COUNT(*) AS count FROM ".static::$table;
       }
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
   }
   
   static function max($fieldName=null){
       if(!is_null($fieldName)){
           $query="SELECT MAX(".$fieldName.") AS max FROM ".static::$table;
       }else{
           $query="SELECT MAX(*) AS max FROM ".static::$table;
       }
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
   }
   
   static function min($fieldName=null){
       if(!is_null($fieldName)){
           $query="SELECT MIN(".$fieldName.") AS min FROM ".static::$table;
       }else{
           $query="SELECT MIN(*) AS min FROM ".static::$table;
       }
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
   }
   
   static function sum($fieldName=null){
       if(!is_null($fieldName)){
           $query="SELECT SUM(".$fieldName.") AS sum FROM ".static::$table;
       }else{
           $query="SELECT SUM(*) AS sum FROM ".static::$table;
       }
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
   }
   
   public function join($tb,$fieldCondition,$tb2=null){
       if(!is_null($tb)){
           if(!is_null($tb2)){
               $this->query.=' JOIN '.$tb.' ON '.$tb.'.'.$fieldCondition.'='.$tb2.'.'.$fieldCondition;
           }else{
               $this->query.=' JOIN '.$tb.' ON '.$tb.'.'.$fieldCondition.'='.static::$table.'.'.$fieldCondition;
           }
           
           return $this;
       }else{
           return false;
       }
   }
   

   public function orderBy($name,$direction='asc'){
       if(!empty($name)){
          $this->query.=" ORDER BY ";
          if(!is_array($name)){
              $obj=$name;
              $name=[];
              $name[]=$obj;
          }
          if(!is_array($direction)){
              $obj=$direction;
              $direction=[];
              $direction[]=$obj;
          }
          $count=1;
          $length=count($name);
          while($itemName = array_shift($name)){
              $break = ', ';
              if($count>=$length){
                  $break='';
              }
              $this->query.=$itemName.' '.array_shift($direction).$break;
              $count++;
          }
          return $this; 
       }else{
           return false;
       }  
   }
   
   public function groupBy($fieldName){
       $this->query.=" GROUP BY ".$fieldName;
       return $this;
   }
   
   public function limit($count){
       $this->query.=" LIMIT ".$count;
       return $this;
   }
   
   public function offset($count){
       $this->query.=" OFFSET ".$count;
       return $this;
   }
   
   
   
   public function where($condition,$param=null){
       if(!empty($condition)){
           $this->query.=" WHERE ".$condition;
       }else{
           return false;
       }
       if(!is_null($param)){
           $count=substr_count($condition, '?');
           if($count!=count($param)){
               return false;
           }
           foreach($param as $item){
               if(is_null($this->dataTypes)){
                   $this->dataTypes="";
               }
               if(is_null($this->data)){
                   $this->data=[];
               }
               $this->dataTypes.=(gettype($item)=='integer'||gettype($item)=='double')?'i':'s';
               $this->data[]=$item;
           } 
       }
       return $this;
   }

    public function query(){
        return $this->query;
    }
   
   public function save(){
        
        $connect = new Connect();
        $class = get_called_class();
        $public=$this->getPublicVars();
        $paramType="";
        $names=$str=$param=[];
        
        foreach ($public as $key => $value) {
            $break = preg_replace('~[A-Z]~u', '/$0', $key);
            $dbField = implode('_',explode('/',strtolower($break)));
            if(is_null($value)){
                continue;
            }
            if($value==="CURRENT_TIMESTAMP"){
                $names[]=$dbField;
                $str[]="CURRENT_TIMESTAMP";
            }else{
                $paramType.=is_integer($value)? 'i' : (is_float($value)|| is_double($value) ? 'd' : 's');
                $names[]=$dbField;
                $str[]="?";
                $param[]=$value;
            }
        }
        $str=implode(',',$str);
        $names=implode(',',$names);
        $result=$connect->sendQuery("INSERT INTO " . static::$table ." (".$names.") VALUES (".$str.")",$paramType,$param);
        return $result;
    }
    
    public function update($isNull=false){
        $connect = new Connect();
        $class = get_called_class();
        $public=$this->getPublicVars();
        $conditions="";
        $paramType=$str="";
        $param=$names=[];
       
        foreach ($public as $key => $value) {
            $break = preg_replace('~[A-Z]~u', '/$0', $key);
            $dbField = implode('_',explode('/',strtolower($break)));
            if(!is_null($class::$primary)&&$class::$primary==$dbField){
                if(gettype($this->{$key})=='integer'){
                     $conditions=" WHERE ".$dbField."=".$value;
                }else{
                     $conditions=" WHERE ".$dbField."='".$value."'";
                }
            }
            if(!$isNull&&is_null($value)){
                continue;
            }else{
                if($dbField!= static::$primary&&is_null($value)){
                    $names[]=$dbField."=NULL";
                }else if($value==="CURRENT_TIMESTAMP"){
                    $names[]=$dbField."=CURRENT_TIMESTAMP";
                }else{
                    $paramType.=(is_integer($value))?'i':(is_float($value)|| is_double($value))?'d':'s';
                    $names[]=$dbField."=?";
                    $param[]=$value;
                }
            }
            
        }
        $names=implode(',',$names);
        $result=$connect->sendQuery("UPDATE " . static::$table ." SET ".$names.$conditions,$paramType,$param);
        if(!$result['success']){
            return false;
        }
        return true;
    }
    
    public static function delete(){
       $query="DELETE FROM ".static::$table;
       $class=get_called_class();
       $new = new $class();
       $new->query=$query;
       return $new;
    }
   
   
   //Получить результат выполнения составленного запроса
   public function send($wordSensetive=true) {
        $connect = new Connect();
        $result = $connect->sendQuery($this->query,$this->dataTypes,$this->data);
        if (!$result['success']) {
            return false;
        }
        if($wordSensetive){
            if(preg_match('~^[^(]+COUNT[^)]+~',$this->query)){
                return mysqli_fetch_assoc($result['data'])['count'];
            }
            if(preg_match('~^[^(]+MAX[^)]+~',$this->query)){
                return mysqli_fetch_assoc($result['data'])['max'];
            }
            if(preg_match('~^[^(]+MIN[^)]+~',$this->query)){
                return mysqli_fetch_assoc($result['data'])['min'];
            }
            if(preg_match('~^[^(]+SUM[^)]+~',$this->query)){
                return mysqli_fetch_assoc($result['data'])['sum'];
            }
        }
        if(preg_match('~^DELETE~',$this->query)){
            return $result;
        }
        if(preg_match('~^SELECT~',$this->query)){
            $objects = self::parse($result['data']);
            return $objects;
        }
        return true;
    }
    
 
    
    public function getPublicVars () {
        $me = new class {
            function getPublicVars($object) {
                return get_object_vars($object);
            }
        };
        return $me->getPublicVars($this);
    }
    
    public static function getPublicVarsClass(){
        $me = new class {
            function getPublicVars($class) {
                return get_class_vars($class);
            }
        };
        return $me->getPublicVars(get_called_class());
    }

    private static function parse($obj) {
        $class = get_called_class();
        $arrObjs=[];
        if(mysqli_num_rows($obj)==0){
            return null;
        }
        while ($row = mysqli_fetch_assoc($obj)) {
            $variables = array_keys($row);
            try {
                $newObj=new $class();
                foreach ($variables as $item) {
                    $arrs = explode('_', $item);
                    $property = "";
                    foreach ($arrs as $str) {
                        $property .= $property != "" ? ucfirst($str) : $str;
                    }
                    $newObj->{$property} = $row[$item];
                }
                $arrObjs[]=$newObj;
            } catch (Exception $ex) {
                return false;
            }
        }
        if(count($arrObjs)==1){
            return $arrObjs[0];
        }else{
            return $arrObjs;
        }
    }
    
    
    public function convertToObj($arr){
        $public=array_keys($this->getPublicVars());
        $class = get_called_class();
        foreach($arr as $key=>$value){
           $prr = explode('_', $key);
           $property = "";
           foreach ($prr as $str) {
               $property .= $property != "" ? ucfirst($str) : $str;
           }
           foreach($public as $item){
               if($item==$property){
                   $this->{$item}=$value;
                   break;
               }
           }
        }
    }
    
    public function convertToArray($isNull=true){
        $public=array_keys($this->getPublicVars());
        $arr=[];
        foreach($public as $item){
           $break = preg_replace('~[A-Z]~u', '/$0', $item);
           $key = implode('_',explode('/',strtolower($break)));
           if(!$isNull){
               if(is_null($this->{$item})) continue;
           }
           $arr[$key]=$this->{$item};
        }
        return $arr;
    }
    
    public static function asArray($obj){
        if(!is_array($obj)){
            $o = $obj;
            $obj=[];
            $obj[]=$o;
        }
        return $obj;
    }

}
