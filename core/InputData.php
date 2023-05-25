<?php

class InputData {

    private static $regular = array("login" => "~^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$~",
        "name" => "~^[A-zА-яЁ-ёҚ-қӘ-әҺ-һІ-іҢ-ңҒ-ғҰ-ұҮ-үӨ-ө]{2,30}[-]{0,1}[A-zА-яЁ-ёҚ-қӘ-әҺ-һІ-іҢ-ңҒ-ғҰ-ұҮ-үӨ-ө]{0,30}$~u",
        "email" => "~^[\w.]+@([A-z0-9][-A-z0-9]+\.)+[A-z]{2,4}$~",
        "pass" => "~^[^А-Яа-яЁё]{8,20}$~",
        "telephone" => "~^(\+)([- _():=+]?\d[- _():=+]?){11,14}(\s*)?$~");

    static function filter($text) {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = strip_tags($text);
        return $text;
    }

    static function validate($type, $variable) {
        foreach (self::$regular as $regName => $reg) {
            if (preg_match("~^.*($regName).*$~", $type)) {
                if (preg_match(self::$regular[$regName], $variable)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return null;
    }

    static function setCookie($name, $value, $expire=0, $path = '/', $domain ="", $secure = false, $httponly = false, $samesite = 'None'){
        $result=false;
        if (PHP_VERSION_ID < 70300) {
            $result = setcookie($name, $value, $expire, $path . '; samesite=' . $samesite, $domain, $secure, $httponly);
            return $result;
        }
        $result = setcookie($name, $value, [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'samesite' => $samesite,
            'secure' => $secure,
            'httponly' => $httponly,
        ]);
        return $result;
    }

    static function getRegular() {
        return self::$regular;
    }

    static function getPost($post = null) {
        if (is_null($post)) {
            $post = $_POST;
        }
        if (isset($post)) {
            $object = array();
            foreach ($post as $key => $value) {
                if (is_array($post[$key])) {
                    $object[$key] = self::getPost($post[$key]);
                } else {
                    $elem = self::filter($value);
                    $object[$key] = $elem;
                }
            }
            return $object;
        } else {
            return false;
        }
    }

    static function getGet($get = null) {
        if (is_null($get)) {
            $get = $_GET;
        }
        if (isset($get)) {
            $object = array();
            foreach ($get as $key => $value) {
                if (is_array($get[$key])) {
                    $object[$key] = self::getGet($get[$key]);
                } else {
                    $elem = self::filter($value);
                    $object[$key] = $elem;
                }
            }
            return $object;
        } else {
            return false;
        }
    }

    static function getServer() {
        $object = array();
        foreach ($_SERVER as $key => $value) {
            $elem = self::filter($value);
            $object[$key] = $elem;
        }
        return $object;
    }

    static function getCookie($cookie = null) {
        if (is_null($cookie)) {
            $cookie = $_COOKIE;
        }
        $object = array();
        foreach ($cookie as $key => $value) {
            if (is_array($cookie[$key])) {
                $object[$key] = self::getCookie($cookie[$key]);
            } else {
                $elem = self::filter($value);
                $object[$key] = $elem;
            }
        }
        return $object;
    }

    static function getUserData($post) {
        $userData = array();
        foreach ($post as $key => $value) {
            if (!empty($value)) {
                $userData[$key] = $value;
                $valid = InputData::validate($key, $userData[$key]);
                if ($valid != null && !$valid) {
                    return false;
                }
            }
        }
        return $userData;
    }
    
    static function randomHash($length, $type) {
        switch ($type) {
            case "chars":$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case "caps-chars":$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case "numbers":$chars = '0123456789';
                break;
            case "chars-numbers":$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case "code":$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case "all": $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@_';
                break;
            default: return false;
        }
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $char = $chars[mt_rand(0, strlen($chars) - 1)];
            $result .= $char;
        }
        return $result;
    }

}
