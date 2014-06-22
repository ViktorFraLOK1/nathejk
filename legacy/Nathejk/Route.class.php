<?php

class Nathejk_Route
{
    protected $path;
    protected $callback;
    protected $methods;
    protected $matches;

    public function __construct($path, $callback, array $methods = null) 
    {
        $this->path = $path;
        $this->callback = $callback;
        if (is_array($methods)) {
            $this->methods = array_map('strtoupper', $methods);
        }
    }

    /**
     * Factory function for easy access
     */
    public static function get($path, $callback)
    {
        return new self($path, $callback, array('GET'));
    }
    public static function post($path, $callback)
    {
        return new self($path, $callback, array('POST'));
    }

    public function accept(Nathejk_Request $request) {
        if ($this->methods && !in_array(strtoupper($request->getMethod()), $this->methods)) {
            return false;
        }
        $pattern = implode('(.+)', array_map('preg_quote', explode('#', $this->path)));
        #var_dump("#^$pattern$#", $request->getPath());
        return preg_match("#^$pattern$#", $request->getPath(), $this->matches);
    }

    public function run(Nathejk_Site $site)
    {
        $args = $this->matches;
        $args[0] = $site;
        return call_user_func_array($this->callback, $args);
    }
}
