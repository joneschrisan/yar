<?php

namespace yar;

class yar {
    const JSON = 1;
    const XML = 2;
    const INI = 3;
    const PLAIN = 4;
    const CSV = 5;
    
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
    
    public function dget_params() {
        if (isset($this->route['params']))
            return $this->route['params'];
        return array();
    }
    
    public function dget_namespace() {
        if (isset($this->route['namespace'])) {
            return $this->route['namespace'];
        } else {
            return "";
        }
    }
    
    public function dget_controller() {
        return $this->route['controller'];
    }
    
    public function dget_method() {
        return $this->route['method'];
    }
    
    public function __construct($use_cache = true, $site = null, $overwrite = false) {
        if ($use_cache === true) {
            $use_cache = sys_get_temp_dir();
        }
            
        if ($use_cache) {
            $this->cache_filename = $use_cache . DIRECTORY_SEPARATOR . ($site ? $site . "_" : "") . "yar_cache.tmp";
            $this->cache_file = new file($this->cache_filename);
            if (strlen($this->cache_file->contents) > 2 && !$overwrite) {
                $this->routes = unserialize($this->cache_file->contents);
            }
        }
    }
    
    public function __destruct() {
        if ($this->cache_filename) {
            $this->cache_file->contents = serialize($this->routes);
            $this->cache_file->save();
        }
    }
    
    public static function recache_routes($routes, $site = null) {
        $o = new self(true, $site, true);
        
        if (is_array($routes)) {
            foreach($routes as $route) {
                if ($is_array($route)) {
                    $route = $route['route'];
                    $controller = $route['controller'] ? $route['controller'] : "default";
                    $method = $route['method'] ? $route['method'] : null;
                    $namespace = $route['namespace'] ? $route['namespace'] : "";
                }
                $o->add_route($route, ($controller ? $controller : "default"), ($method ? $method : null), ($namespace ? $namespace : ""));
            }
        } else {
            $o->add_routes_from_file($routes);
        }
        
        $o->__destruct();
    }
    
    public function add_routes_from_file($filename, $type = self::JSON) {
        $file = new file($filename);
        
        $routes = $this->get_file_contents($file, $type);
        
        if ($routes) {
            foreach($routes as $route) {
                $this->routes[$route['route']] = $this->route_to_regex($route);
            }
        } else {
            throw new YarException("No routes given", 1);
        }
    }
    
    public function add_route($route, $controller = "default", $method = null, $namespace = "") {
        $route = array(
            "route" => $route,
            "controller" => $controller,
            "method" => $method
        );
        
        if ($namespace) {
            $route['namespace'] = $namespace;
        }
        
        $this->routes[$route['route']] = $this->route_to_regex($route);
    }
    
    public function remove_route($route) {
        unset($this->routes[$route]);
    }
    
    public function find_route($request_route) {
        $matches = null;
        $count = count(explode("/", $request_route));
        $tmp = null;
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
        if ($tmp) {
            return true;
        }
        
        throw new YarException("No route found", 1);
    }
    
    public function render($template_engine = null, $params = array()) {
        $controller_class_name = "";
        if ($this->namespace) {
            $controller_class_name .= "\\" . $this->namespace . "\\controllers\\";
        }
        $controller_class_name .= $this->controller;
        $controller = new $controller_class_name($template_engine);
        $method = $this->method;
        $params = array_merge($this->params, $params);
        return $controller->$method($params);
    }
    
    private function get_file_contents($file, $type) {
        $contents = "";
        switch($type) {
            case self::JSON:
                $contents = json_decode($file->contents, true);
                break;
            case self::XML:
                // ToDo
                break;
            case self::INI:
                // ToDo
                break;
            case self::PLAIN:
                // ToDo
                break;
            case self::CSV:
                // ToDo
                break;
        }
        return $contents;
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
