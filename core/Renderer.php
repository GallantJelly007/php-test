<?php

class Renderer {

    static function render($path, $vars = array()) {
        if (file_exists($path)) {
            ob_start();
            extract($vars);
            require $path;
            return ob_get_clean();
        }
    }

}
