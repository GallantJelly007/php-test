<?php

require_once ROOT.'/controllers/Controller.php';
require_once ROOT.'/behaviours/Main.php';
require_once ROOT.'/models/Object.php';

class MainController extends Controller{


    public function view(){
        $result =  Main::actionView();
        echo Renderer::render(ROOT.'/views/main.php',['authButton'=>$this->authButton,
                                                      'authPanel'=>$this->authPanel,
                                                      'navAdminButtons'=>$this->navAdminButtons,
                                                      'content'=>$result['content']]);
        return true;
    }

    public function authorization(){
        if(!isset($_SESSION['auth_user'])){
            $server= InputData::getServer();
            $result = Auth::authorization();
            if($result['success']==1){
                $user=$result['user'];
                $result = Token::gen($user, true);
                InputData::setCookie('access_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                InputData::setCookie('refresh_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                $c1=InputData::setCookie('access_token', $result['access_token'], time() + (3600 * 24 * LTRT), '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                $c2=InputData::setCookie('refresh_token', $result['refresh_token'], time() + (3600 * 24 * LTRT), '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                if(!$c1||!$c2){
                    echo json_encode(['success' => '0', 'message' => 'Не удалось установить Cookie!'],JSON_UNESCAPED_UNICODE);
                }
                $_SESSION['auth_user'] =(array)$user;
                echo json_encode(['success' => '1'],JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            }
            return true;
        }
        return false;
    }

    public function registration(){
        echo json_encode(Auth::registration(),JSON_UNESCAPED_UNICODE);
        return true;
    }

    public function saveObject(){
        if(isset($_SESSION['auth_user'])&&$_SESSION['auth_user']['role']=='admin'){
            echo json_encode(Main::save(),JSON_UNESCAPED_UNICODE);
            return true;
        }
        return false;
    }
    public function deleteObject(){
        if(isset($_SESSION['auth_user'])&&$_SESSION['auth_user']['role']=='admin'){
            echo json_encode(Main::delete(),JSON_UNESCAPED_UNICODE);
            return true;
        }
        return false;
    }

    public function logout(){
        if(isset($_SESSION['auth_user'])){
            echo Auth::logout();
            return true;
        }
        return false;
    }
}