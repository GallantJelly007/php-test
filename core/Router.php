<?php

class Router {
    
    private $routes;

    public function __construct($routes) {
        $server = InputData::getServer();
        $host=explode('.',$server['HTTP_HOST']);
        $host=array_shift($host);
        $this->routes = $routes;
    }
    
    private function getURI(){
        $server= InputData::getServer();
        if(!empty($server['REQUEST_URI'])){
            return trim($server['REQUEST_URI'], '/');
        }
    }

    public function run(){
        $check=true;
        $uri = $this->getURI();
        foreach($this->routes as $uriPattern=>$path){
            if(preg_match("~^$uriPattern$~",$uri)){
                if($uriPattern!=$uri){
                    $route = preg_replace("~$uriPattern~",$path, $uri); //если есть шаблоны
                    $route = explode('/', $route);
                }else{
                    $route = explode('/', $path);  //если нет шаблонов
                }
                $nameController = array_shift($route);
                $nameAction = array_shift($route); 
                if(file_exists(ROOT.'/controllers/'.$nameController.'.php')){
                    require_once ROOT.'/controllers/'.$nameController.'.php';
                    $controller = new $nameController();
                    $result = $controller->$nameAction($route);
                    if($result){
                        $check=false;
                        break;
                    }else if($result==null||$result==false){
                        $check=false;
                        http_response_code(404);
                        break;
                    }
                }else{
                    $check=false;
                    http_response_code(404);
                    break;
                }               
            }else{
                $check=true;
            }    
        }
        if($check){
            http_response_code(404);
        }
    }
}
