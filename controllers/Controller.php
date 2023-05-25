<?php

require_once ROOT . '/models/User.php';
require_once ROOT . '/behaviours/Auth.php';

class Controller {

    public $authPanel = null;
    public $navAdminButtons = null;
    public $authButton = null;

    public function __construct() {

        if (!isset($_SESSION['auth_user'])) {
            Auth::authToken();
        }
        if (isset($_SESSION['auth_user'])) {
            if ($this->verifyUser()) {
                $user = $_SESSION['auth_user'];
                $userName = $user['name'].' '.$user['surname'];
                $this->authButton = Renderer::render(ROOT.'/views/patterns/auth_user.php',['userName'=>$userName]);
                if ($user['role'] == "admin") {
                    $this->navAdminButtons = Renderer::render(ROOT . '/views/patterns/admin_buttons.php',['root'=>true]);
                }
            } else {
                if (isset($_SESSION['auth_user'])) {
                    unset($_SESSION['auth_user']);
                }
                $this->authButton = Renderer::render(ROOT.'/views/patterns/auth_button.php');
                $this->authPanel = Renderer::render(ROOT.'/views/patterns/auth.php');
            }
        } else {
            $this->authButton = Renderer::render(ROOT.'/views/patterns/auth_button.php');
            $this->authPanel = Renderer::render(ROOT.'/views/patterns/auth.php');
        }
    }

    public function verifyUser() {
        try {
            $cookie = InputData::getCookie();
            if (isset($cookie['access_token']) && isset($cookie['refresh_token'])) {
                $info = Token::decode($cookie['access_token']);
                $infoRefresh = Token::decode($cookie['refresh_token']);
                if ($info['data']['id'] != $infoRefresh['data']['id']) {
                    return false;
                }
                $user = User::select()->where('id=?', [$info['data']['id']])->send();
                if ($user === false) {
                    return false;
                }
                if (is_null($user)) {
                    return false;
                }
                if(!isset($_SESSION['auth_user'])){
                    return false;
                }
                if ($user->id != $_SESSION['auth_user']['id']) {
                    return false;
                }

                $result = Token::verify($cookie['access_token'], $user->keyToken);
                if ($result['success'] == 0) {
                    $result = Token::verify($cookie['refresh_token'], $user->keyRtoken, true);
                    if ($result['success'] == 1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

}
