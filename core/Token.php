<?php

class Token {

    static public function encode($data,$key,$refresh=false) {

        $header = ["alg" => "SHA256"];
        $segments = [];
        $segments[] = base64_encode(json_encode($header));
        $segments[] = base64_encode(json_encode($data));
        $sign_msg = implode('.', $segments);
        $sign = "";
        if ($refresh) {
            $sign = hash_hmac("SHA256", $sign_msg, $key, true);
        } else {
            $sign = hash_hmac("SHA256", $sign_msg, $key, true);
        }

        $segments[] = base64_encode($sign);
        $result=implode('.', $segments);
        return rawurlencode($result);
    }

    static public function decode($token) {
        try{
            $token= rawurldecode($token);
            $data = [];
            $segments = explode('.', $token);
			if(count($segments)<3){
				return false;
			}
            $data = json_decode(base64_decode($segments[1]),true);
            return $data;
        } catch (Exception $ex) {
            return false;
        }
    }

    static public function verify($token,$key,$refresh=false) {
        $data = [];
        $token= rawurldecode($token);
        $segments = explode('.', $token);
        $data['header'] = json_decode(base64_decode($segments[0]),true);
        $data['body'] = json_decode(base64_decode($segments[1]),true);
        if ($data['body']['exp'] < time()) {
            return ['success' => 0, 'message' => 'Время действия токена истекло'];
        }
        $sign_msg = $segments[0] . '.' . $segments[1];
        $sign = "";
        $sign = hash_hmac("SHA256", $sign_msg, $key, true);
        if ($segments[2] != base64_encode($sign)) {
            return ['success' => 0, 'message' => 'Недействительный токен'];
        }
        return ['success' => 1];
    }

    static public function gen($user, $refresh = false) {
        $token=$refreshToken="";
        $userClone = get_object_vars($user);
        unset($userClone['keyRToken']);
        unset($userClone['keyToken']);
        $data = array(
            "iss" => DOMAIN,
            "gen" => time(),
            "exp" => time() + (3600 * 24 * LTT),
            "data" => (array) $userClone
        );
        $token = Token::encode($data,$user->keyToken);
        if($refresh){
            $data['exp'] = time() + (3600 * 24 * LTRT);
            $refreshToken = Token::encode($data,$user->keyRtoken, true);
            return ['success' => 1, 'access_token' => $token, 'refresh_token' => $refreshToken];
        }else{
            return ['success' => 1, 'access_token' => $token];
        }
        
    }

}
