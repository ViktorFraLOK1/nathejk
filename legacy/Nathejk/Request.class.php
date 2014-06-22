<?php

class Nathejk_Request
{
    private $server = array();
    private $get = array();
    private $post = array();
    private $cookie = array();
    private $file = array();
	
    public function __construct($server, $get, $post, $cookie, $file)
	{
        $this->server = $server;
        $this->get = $get;
        $this->post = $post;
        $this->cookie = $cookie;
        $this->file = $file;
        //die($this->_server['REMOTE_ADDR']);
	}

    public function request($variableName = null, $defaultValue = null, $methodName = null)
    {
        $methodName = strtolower($methodName);
        if (in_array($methodName, array('server', 'get', 'post', 'cookie'))) {
            $variables = $this->$methodName;
        } else if ($methodName === null) {
            // be aware from 5.3 you can control request order http://www.php.net/manual/en/ini.core.php#ini.request-order
            $variables = array_merge($this->cookie, $this->post, $this->get);
        } else {
            $variables = array();
        }
        if ($variableName === null) {
            return $variables;
        }
        if (isset($variables[$variableName])) {
            return $variables[$variableName];
        }
        return $defaultValue;
    }
    public function server($variableName = null, $defaultValue = null)
    {
        return $this->request($variableName, $defaultValue, 'server');
    }
    public function post($variableName = null, $defaultValue = null)
    {
        return $this->request($variableName, $defaultValue, 'post');
    }
    public function get($variableName = null, $defaultValue = null)
    {
        return $this->request($variableName, $defaultValue, 'get');
    }
    public function file($variableName = null)
    {
        $variables = $this->file;
        if ($variableName === null) {
            return $variables;
        }
        if (isset($variables[$variableName])) {
            return $variables[$variableName];
        }
        return null;
    }
    

    public function getHostName()
    {
        return $this->server('HTTP_HOST');
    }
    public function getMethod()
    {
        return $this->server('REQUEST_METHOD');
    }

    public function getPath()
    {
        $prefix = array($this->server('SCRIPT_NAME'), dirname($this->server('SCRIPT_NAME')));
        foreach ($prefix as $scriptName) {
            if (strpos($this->server('PHP_SELF'), $scriptName) === 0) {
                return substr($this->server('PHP_SELF'), strlen($scriptName));
            }
        }

        return $this->server('PHP_SELF');
    }

    public function getPostValue($key, $defaultValue = null)
    {
        if (isset($this->_post[$key])) {
            return $this->_post[$key];
        }
        return $defaultValue;
    }
    public function getGetValue($key)
    {
        if (isset($this->_get[$key])) {
            return $this->_get[$key];
        }
        return null;
    }
}

?>
