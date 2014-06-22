<?php

class Nathejk_Site
{
    protected $request;
    protected $routes = array();

    public function __construct(Nathejk_Request $request)
    {
        $this->request = $request;
    }

    public function addRoute(Nathejk_Route $route)
    {
        $this->routes[] = $route;
    }

    public function findRoute($path = null)
    {
        if ($path === null) {
            $path = $this->request->getPath();
        }
        foreach ($this->routes as $route) {
            if ($route->accept($this->request)) {
                return $route;
            }
        }
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function sendResponse()
    {
        $route = $this->findRoute();
        if (!$route) {
            // 404 page not found
            print 404;
            return;
        }
        print $route->run($this);
    }
}

?>
