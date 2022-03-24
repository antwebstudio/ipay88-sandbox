<?php

namespace Ant\Sandbox;

class Response
{
    public $url;
    public $params;
    public $method = 'POST';

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function addParam($name, $value)
    {
        $this->params[$name] = $value;
    }
}
