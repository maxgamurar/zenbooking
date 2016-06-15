<?php

class Controller {

    protected $_model, $_controller, $_action;
    public $cfg, $view, $table, $id, $db, $userValidation;

    public function __construct($model = "Model", $controller = "Controler", $action = "index") {
        global $cfg;

        // Config
        $this->cfg = $cfg;

        // INIT MVC
        $this->_controller        = $controller;
        $this->_action            = $action;
        // template class
        $this->view               = new Template($controller, $action);
        // start initialization   
        $this->init();
        // construct models
        $this->_model             = new $model($this->db);
        $this->_model->controller = $this;
        $this->table              = $controller;
    }

    /**
     * Initialize classes and variables
     */
    protected function init() {
        /* some init code here */
    }

    /**
     * Redirect to action
     */
    public function redirectToAction($action, $controller = false, $params = array()) {
        if ($controller === false) {
            $controller = get_called_class();
        } else if (is_string($controller) && class_exists($controller . 'Controller')) {
            $controller = $controller . 'Controller';
            $controller = new $controller();
        }
        return call_user_func_array(array($controller, $action), $params);
    }

    /**
     * process default action view
     */
    public function defaultAction($params = null) {

        $path = MyHelpers::UrlContent("~/views/{$this->_controller}/{$this->_action}.php");
        if (file_exists($path)) {
            $this->view->viewPath = $path;
        } else {
            $this->unknownAction();
        }
        // if we have paramaters -  assign local variables
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->view->set($key, $value);
            }
        }
        return $this->view();
    }

    /**
     * unknownAction - feed 404 header to the client
     */
    public function unknownAction($params = array()) {
        header("HTTP/1.0 404 Not Found");
        $path = MyHelpers::UrlContent("~/views/shared/_404.php");
        if (file_exists($path)) {
            $this->view->viewPath = $path;
            return $this->view();
        } else {
            exit;
        }
    }

    /**
     * set the variables
     */
    public function set($name, $value) {
        $this->view->set($name, $value);
    }

    /**
     * Returns the template result
     */
    public function view() {
        return $this->view;
    }

}
