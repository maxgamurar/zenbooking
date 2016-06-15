<?php

/* request router parser */

class Router {

    protected $_controller, $_action, $_view, $_params, $_route;

    public function __construct($_route) {
        $this->_route      = $_route;
        $this->_controller = 'Controller';
        $this->_action     = 'index';
        $this->_params     = array();
        $this->_view       = false;
    }

    private function parseRoute() {
        $id = false;

        if (isset($this->_route)) {

            $path = $this->_route;
            // the rules to route
            $cai  = '/^([\w]+)\/([\w]+)\/([\d]+).*$/';  //  controller/action/id
            $ci   = '/^([\w]+)\/([\d]+).*$/';           //  controller/id
            $ca   = '/^([\w]+)\/([\w]+).*$/';           //  controller/action
            $c    = '/^([\w]+).*$/';                    //  action
            $i    = '/^([\d]+).*$/';                    //  id

            $matches = array();

            if (empty($path)) {
                $this->_controller = 'index';
                $this->_action     = 'index';
            } else if (preg_match($cai, $path, $matches)) {
                $this->_controller = $matches[1];
                $this->_action     = $matches[2];
                $id                = $matches[3];
            } else if (preg_match($ci, $path, $matches)) {
                $this->_controller = $matches[1];
                $id                = $matches[2];
            } else if (preg_match($ca, $path, $matches)) {
                $this->_controller = $matches[1];
                $this->_action     = $matches[2];
            } else if (preg_match($c, $path, $matches)) {
                $this->_controller = $matches[1];
                $this->_action     = 'index';
            } else if (preg_match($i, $path, $matches)) {
                $id = $matches[1];
            }

            $query = array();
            $parse = parse_url($path);
            if (!empty($parse['query'])) {
                parse_str($parse['query'], $query);
                if (!empty($query)) {
                    $_GET     = array_merge($_GET, $query);
                    $_REQUEST = array_merge($_REQUEST, $query);
                }
            }
        }

        $method = $_SERVER["REQUEST_METHOD"];

        switch ($method) {
            case "GET":
                unset($_GET['_route']);
                $this->_params = array_merge($this->_params, $_GET);
                break;
            case "POST":
            case "PUT":
            case "DELETE": {

                    if (!array_key_exists('HTTP_X_FILE_NAME', $_SERVER)) {
                        if ($method == "POST") {
                            $this->_params = array_merge($this->_params, $_POST);
                        } else {

                            $p             = array();
                            $content       = file_get_contents("php://input");
                            parse_str($content, $p);
                            $p             = json_decode($content, true);
                            $this->_params = array_merge($this->_params, $p);
                        }
                    }
                }
                break;
        }

        if (!empty($id)) {
            $this->_params['id'] = $id;
        }

        if ($this->_controller == 'index') {
            $this->_params = array($this->_params);
        }
    }

    public function dispatch() {

        $this->parseRoute();

        $controllerName    = $this->_controller;
        $model             = $this->_controller . 'Model';
        $model             = class_exists($model) ? $model : 'Model';
        $this->_controller .= 'Controller';
        $this->_controller = class_exists($this->_controller) ? $this->_controller : 'Controller';
        $dispatch          = new $this->_controller($model, $controllerName, $this->_action);
        $hasActionFunction = (int) method_exists($this->_controller, $this->_action);

        $c          = new ReflectionClass($this->_controller);
        $m          = $hasActionFunction ? $this->_action : 'defaultAction';
        $f          = $c->getMethod($m);
        $p          = $f->getParameters();
        $params_new = array();
        $params_old = $this->_params;

        // re-map the parameters
        for ($i = 0; $i < count($p); $i++) {
            $key = $p[$i]->getName();
            if (array_key_exists($key, $params_old)) {
                $params_new[$i] = $params_old[$key];
                unset($params_old[$key]);
            }
        }

        $params_new = array_merge($params_new, $params_old);

        $this->_view = call_user_func_array(array($dispatch, $m), $params_new);

        if ($this->_view) {
            echo $this->_view;
        }
    }

}
