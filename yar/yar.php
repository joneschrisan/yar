<?php

namespace yar;

class yar {
    const JSON = 1;
    const XML = 2;
    const INI = 3;
    const PLAIN = 4;
    
    private $cach_filename = null;
    private $cache_file = null;
    private $routes = array();
    private $route;
    
    public function __get($name) {
        $method = "dget_" . $name;
        if (method_exists($this, $method))
            return $this->$method();
    }
    
    public function dget_routes() {
        return $this->routes;
    }
    
    public function dget_route() {
        return $this->route;
    }
    
    public function dget_namespace() {
        return $this->route['namespace'];
    }
    
    public function dget_controller() {
        return $this->route['controller'];
    }
    
    public function dget_method() {
        return $this->route['method'];
    }
    
    public function __construct($use_cache = true, $site = null, $overwrite = false) {
        if ($use_cache === true)
            $use_cache = sys_get_temp_dir();
            
        if ($use_cache) {
            $this->cache_filename = $use_cache . DIRECTORY_SEPARATOR . ($site ? $site . "_" : "") . "yar_cache.tmp";
            $this->cache_file = new file($this->cache_filename);
            //if (strlen($this->cache_file->contents) > 2 && !$overwrite) {
            //    $this->routes = unserialize($this->cache_file->contents);
            //}
        }
    }
    
    public function __destruct() {
        if ($this->cache_filename) {
            $this->cache_file->contents = serialize($this->routes);
            $this->cache_file->save();
        }
    }
    
    public function add_routes_from_file($filename) {
        $tmp = new file($filename);
        
        $tmpArr = json_decode($tmp->contents, true);
        
        foreach($tmpArr as $route) {
            $this->routes[$route['route']] = $this->route_to_regex($route);
        }
    }
    
    public function add_route($route, $controller = "default") {
        $this->routes[$route] = array(
            "route" => $route,
            "controller" => $controller
        );
    }
    
    public function remove_route($route) {
        unset($this->routes[$route]);
    }
    
    public function find_route($request_route) {
        $matches = null;
        $count = count(explode("/", $request_route));
        foreach($this->routes as $key => $route) {
            if ($count == count(explode("/", $route['route']))) {
                $matches = array();
                preg_match_all("!" . $route['route'] . "!", $request_route, $matches);
                $tmp = array_shift($matches);
                if ($tmp) {
                    if($matches) {
                        $i = 0;
                        foreach($this->routes[$key]['params'] as $key2 => &$param) {
                            $param = $matches[$i][0];
                            $i++;
                        }
                    }
                    $this->route = $this->routes[$key];
                    break;
                }
            }
        }
        
        if ($matches)
            return true;
        throw new \yar\YarException("No route found", 1);
    }
    
    private function route_to_regex($route) {
        $matches = array();
        preg_match_all("/{([a-z|A-Z|0-9|-|_].*?)(:[i|d|s]|)}/", $route['route'], $matches);
        
        if ($matches[0]) {
            $route['params'] = array();
            $route = $this->slug_to_regex($route, $matches[0], $matches[1]);
        }
        
        return $route;
    }
    
    private function slug_to_regex($route, $match0, $match1) {
        if (is_array($match0)) {
            $len = count($match1);
            for($i = 0; $i < $len; $i++) {
                $route = $this->slug_to_regex($route, $match0[$i], $match1[$i]);
            }
        } else {
            $replaces = "([a-z|A-Z|0-9|-|_|%|\+|\.].*)";
            if (strpos($match0, ":") !== false) {
                $tmp = explode(":", substr($match0, 1, -1));
                if ($tmp[1] == "i") {
                    $replaces = "([0-9|-|\+].*)";
                }
                if ($tmp[1] == "d") {
                    $replaces = "([0-9|-|\+|\.].*)";
                }
            }
            $route['params'][$match1] = null;
            $route['route'] = str_replace($match0, $replaces, $route['route']);
        }
        return $route;
    }
}

?>