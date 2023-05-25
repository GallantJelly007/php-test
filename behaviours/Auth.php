<?php

require_once ROOT . '/core/Model.php';
require_once ROOT . '/models/User.php';
require_once ROOT . '/models/UserJournal.php';

class Auth {

    static function registration() {
        $dataNames = ['name', 'surname', 'email', 'password','policy'];
        $post = InputData::getPost();
        $userData = InputData::getUserData($post);
        if (!$userData) {
            return ['success' => 0, 'message' => 'Не верно введены данные'];
        }
        foreach ($userData as $key => $value) {
            foreach ($dataNames as $item) {
                if ($item == $key && !empty($value)) {
                    unset($dataNames[array_search($item, $dataNames)]);
                }
            }
        }
        if (count($dataNames) > 0) {
            return ['success' => 0, 'message' => 'Не все поля заполнены'];
        }
        $count = User::count('email')->where('email=?', [$userData['email']])->send();
        if ($count === false) {
            return ['success' => 0, 'message' => 'Ошибка сервера'];
        }
        if ($count > 0) {
            return ['success' => 0, 'message' => 'Введенный почтовый ящик используется другой учётной записью'];
        }
        $user = new User();
        $user->email= $userData['email'];
        $user->pass = hash("sha512", $userData['password']);
        $user->name = $userData['name'];
        $user->surname = $userData['surname'];
        $result = $user->save();

        if (!$result['success']) {
            return ['success' => 0, 'message' => 'Ошибка сервера'];
        }
        return ['success' => 1, 'message' => 'Успешная регистрация'];
    }

    static function rewriteToken($token = null, $refreshToken = null) {
        if (!is_null($token)) {
            $info = Token::decode($token);
            $user = User::select()->where('id=?', [$info['data']['id']])->send();
            if ($user === false) {
                return ['success' => 0, 'message' => 'Ошибка проверки токена'];
            }
            if (is_null($user)) {
                return ['success' => 0, 'message' => 'Пользователь не найден'];
            }
            $result = Token::verify($token, $user->keyToken);
            if ($result['success'] == 0) {
                if (!is_null($refreshToken)) {
                    $result = Token::verify($refreshToken, $user->keyRtoken, true);
                    if ($result['success'] == 1) {
                        return Token::gen($user, true);
                    } else {
                        return ['success' => 0, 'message' => 'Токены не действительны'];
                    }
                } else {
                    return ['success' => 0, 'message' => 'access_token истёк, отправьте вместе с ним refresh для получения нового'];
                }
            } else {
                return Token::gen($user);
            }
        }
        return ['success' => 0, 'message' => 'Не отправлен токен доступа'];
    }

    static function authToken() {
        $cookie = InputData::getCookie();
        $server= InputData::getServer();
        if (isset($cookie['access_token']) && isset($cookie['refresh_token'])) {
            $result = self::rewriteToken($cookie['access_token'], $cookie['refresh_token']);
            echo '<br><br><br><br><br><br>';
            var_dump($result);
            if ($result['success'] == 1) {
              
                if (isset($result['refresh_token'])) { 
                    InputData::setCookie('refresh_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                    InputData::setCookie('refresh_token', $result['refresh_token'], time() + (3600 * 24 * 7), '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                }
                InputData::setCookie('access_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                InputData::setCookie('access_token', $result['access_token'], time() + (3600 * 24), '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
                $info = Token::decode($cookie['access_token']);
                $_SESSION['auth_user'] = $info['data'];
            }
        }
        return false;
    }

    static function authorization() {
        $server = InputData::getServer();
        $os = self::getOS($server['HTTP_USER_AGENT']);
        $timestamp = date("d.m.y, в H:i:s");
        $data=InputData::getPost();
        $server = InputData::getServer();
       
        if (isset($data['login']) && isset($data['pass']) && !empty($data['login']) && !empty($data['pass'])) {
            $email= $data['login'];
            $pass = $data['pass'];
            $user = User::select()->where('email=?', [$email])->send();
            if ($user === false) {
                return ['success' => 0, 'message' => 'Ошибка сервера'];
            }
            if (is_null($user)) {
                return ['success' => 0, 'message' => 'Аккаунт с таким логином не найден'];
            }
            if ($user->pass == hash("sha512", $pass)) {
                if(is_null($user->keyToken)||$user->keyToken==""){
                    $user->keyToken = InputData::randomHash(64, 'all');
                    $user->keyRtoken = InputData::randomHash(64, 'all');
                    $user->update();
                }
                $userLog = self::writeEnter($user->id);
                if (!$userLog) {
                    return ['success' => '0', 'message' => 'Ошибка сервера'];
                }
                return ['success' => 1, 'user' => $user];
            } else {
                return ['success' => 0, 'message' => 'Неверный логин или пароль'];
            }
        } else {
            return ['success' => '0', 'message' => 'Не все поля заполнены'];
        }
    }
    


    static function writeEnter($userID) {
        $server = InputData::getServer();
        $os = self::getOS($server['HTTP_USER_AGENT']);
        $log = new UserJournal();
        $log->userId = $userID;
        $log->os = $os;
        $log->ip = $server['REMOTE_ADDR'];
        $result = $log->save();
        return $result['success'];
    }

    static function getOS($userAgent) {
        try{
           $oses = require ROOT . '/core/oses.php';
            foreach ($oses as $os => $pattern) {
                if (preg_match("~$pattern~", $userAgent)) {
                    return $os;
                }
            }
            return 'Unknown';
        } catch (Exception $ex) {
            return 'Unknown';
        }
    }

    static function logout(){
        $server=InputData::getServer();
        InputData::setCookie('access_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
        InputData::setCookie('refresh_token', '', -1, '/', $server['HTTP_HOST'], COOKIE_SECURE, COOKIE_ONLYHTTP, 'Lax');
        session_destroy();
        $_SERVER['REQUEST_URI'] = "";
        header("Location: " . DOMAIN, true);
    }
}
