<?php

require_once ROOT . '/core/Model.php';
require_once ROOT . '/models/User.php';
require_once ROOT . '/models/UserJournal.php';
require_once ROOT . '/models/Object.php';

class Main{
    static function actionView(){
        try{
            $objs = Obj::select()->send();
            if($objs===false||is_null($objs)){
                $content = Renderer::render(ROOT.'/views/patterns/no_data.php');
                return ['success'=>1,'content'=>$content];
            }
            $objs = Obj::asArray($objs);

            $recursiveTree = function($current,$mainArr) use ( &$recursiveTree ){
                $content='';
                while($item = array_shift($current)){
                    $childrens = array_filter($mainArr,function($el) use ($item){
                        return ($el->parentId==$item->id);
                    });

                    $childIDS=array_map(function($el){ return $el->id; },$childrens);
                    $current = array_filter($current,function($el) use ($childIDS){
                        return !in_array($el->id,$childIDS);
                    });
                   
                    if(count($childrens)>0){
                        $childContainer = Renderer::render(ROOT.'/views/patterns/obj_container.php',['root'=>false,'parentId'=>$item->id,'items'=>$recursiveTree($childrens,$mainArr)]);
                        $content .= Renderer::render(ROOT.'/views/patterns/obj.php',['isContainsChild'=>true,'obj'=>$item,'children'=>$childContainer]);
                    }else{
                        $content .= Renderer::render(ROOT.'/views/patterns/obj.php',['isContainsChild'=>false,'obj'=>$item,'children'=>null]);
                    }
                }
                return $content;
            };
            $content='';
            $rootObjs = array_filter($objs,function($el){
                return ($el->parentId==null);
            });
            $content = Renderer::render(ROOT.'/views/patterns/obj_container.php',['root'=>true,'parentId'=>null,'items'=>$recursiveTree($rootObjs,$objs)]);
            return ['success'=>1,'content'=>$content];
        }catch(Exception $ex){
            $content = Renderer::render(ROOT.'/views/patterns/no_data.php');
            return ['success'=>0,'content'=>$content];
        }
    }

    static function save(){
        $post = InputData::getPost();
        $message = 'Ошибка сервера';
        try{
            if(!isset($post['title'])||empty($post['title'])||!isset($post['description'])||empty($post['description'])){
                throw new Exception();
            }
            if(isset($post['id'])){
                $obj = Obj::select()->where('id=?',[$post['id']])->send();
                if(is_null($obj)||$obj==false){
                    throw new Exception();
                }
                $obj->title = $post['title'];
                $obj->description = $post['description'];
                $result = $obj->update();
                if(!$result){
                    throw new Exception();
                }
                return ['success'=>1];
            }else{
                $obj = new Obj();
                $obj->title = $post['title'];
                $obj->description = $post['description'];
                $obj->userId = $_SESSION['auth_user']['id'];
                if(isset($post['parentId'])){
                    $obj->parentId = $post['parentId'];
                }
                $result = $obj->save();
                if(!$result['success']){
                    throw new Exception();
                }
                return ['success'=>1,'newId'=>$result['insertId']];
            }
        }catch(Exception $ex){
            return ['success'=>0,'message'=>$message];
        }
    }

    static function delete(){
        $post = InputData::getPost();
        $message = 'Ошибка сервера';
        try{
            if(!isset($post['id'])||empty($post['id'])){
                throw new Exception();
            }
            $result = Obj::delete()->where('id=?',[$post['id']])->send();
            if(!$result){
                throw new Exception();
            }
            $count = Obj::count()->send();
            if($count==0){
                $content = Renderer::render(ROOT.'/views/patterns/no_data.php');
                return ['success'=>1,'content'=>$content];
            }
            return ['success'=>1];
        }catch(Exception $ex){
            return ['success'=>0,'message'=>$message];
        }
    }
}


