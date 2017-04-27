<?php

class Request {

    protected $post = array();
    protected $get = array();
    protected $server = array();
    protected $session = array();

    public function getPost($key) {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
    }

    public function setPost($data = array()) {
        foreach ($data as $key => $value) {
            $this->post[$key] = $value;
        }
    }

    public function getGet($key) {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
    }

    public function setGet($data = array()) {
        foreach ($data as $key => $value) {
            $this->get[$key] = $value;
        }
    }

    public function getServer($key) {
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }
    }

    public function setServer($data = array()) {
        foreach ($data as $key => $value) {
            $this->server[$key] = $value;
        }
    }

    public function getSession($key) {
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }
    }

    public function setSession($data = array()) {
        foreach ($data as $key => $value) {
            $this->session[$key] = $value;
        }
    }

}
