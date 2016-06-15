<?php
/* Basic template parser */
class Template {

    protected $_variables = array(), $_controller, $_action, $_bodyContent;
    public $viewPath, $section       = array(), $layout;

    public function __construct($controller, $action) {
        $this->_controller = $controller;
        $this->_action     = $action;
        global $cfg;
        $this->set('cfg', $cfg);
    }

    /**
     * Set Variables
     */
    public function set($name, $value) {
        $this->_variables[$name] = $value;
    }

    /**
     * set Action
     */
    public function setAction($action) {
        $this->_action = $action;
    }

    /**
     * RenderBody and out
     */
    public function renderBody() {
        // if we have content, then deliver it
        if (!empty($this->_bodyContent)) {
            echo $this->_bodyContent;
        }
    }

    /**
     * RenderSection and out
     */
    public function renderSection($section) {
        if (!empty($this->section) && array_key_exists($section, $this->section)) {
            echo $this->section[$section];
        }
    }

    /**
     * Display Template
     */
    public function render() {

        extract($this->_variables);
        $path = MyHelpers::UrlContent('~/views/');
        ob_start();
        if (empty($this->viewPath)) {
            include ($path . $this->_controller . DS . $this->_action . '.php');
        } else {
            include ($this->viewPath);
        }
        $this->_bodyContent = ob_get_contents();
        ob_end_clean();
        ob_start();
        if (!empty($this->layout) && (!MyHelpers::isAjax())) {
            $this->layout = MyHelpers::UrlContent($this->layout);
            include($this->layout);
        } else {
            echo $this->_bodyContent;
        }
        ob_end_flush();
    }

    /**
     * return the renderred html
     */
    public function __toString() {
        $this->render();
        return '';
    }

}
