<?php
class yar_autoloader {
    
    private static $autoloader_set = false;
    
    public static function register($prepend = false) {
        $error = false;
        if (self::$autoloader_set) {
            trigger_error("Auto loader already set", E_USER_NOTICE);
        } else {
            self::add_path(__DIR__ . DIRECTORY_SEPARATOR . "..");
            
            $class_extensions = spl_autoload_extensions();
            if (strpos($class_extensions, ".php") === false) {
                spl_autoload_extensions($class_extensions . ",.php");
            }
            
            if (is_array(spl_autoload_functions()) && in_array("spl_autoload", spl_autoload_functions())) {
                trigger_error("Auto loader already set", E_USER_NOTICE);
            } else {
                if (PHP_VERSION_ID < 50300) {
                    if (!spl_autoload_register("spl_autoload")) {
                        $error = true;
                    }
                } else {
                    if (!spl_autoload_register("spl_autoload", true, $prepend)) {
                        $error = true;
                    }
                }
            }
                
            if ($error) {
                trigger_error("Could not add autoloader to the queue", E_USER_NOTICE);
            } else {
                self::$autoloader_set = true;
            }
        }
    }
    
    public static function add_path($path) {
        $path = realpath($path);
        $paths = get_include_path();
        if (strpos($paths, $path) === false) {
            $class_paths = array(
                $paths,
                $path
            );
            set_include_path(join(PATH_SEPARATOR, $class_paths));
        }
    }
}
?>
