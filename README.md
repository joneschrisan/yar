# YAR
### Yet Another Router

My first attempt at a website router. I'm writing this as a learning experiance.

I'm releasing it under the MIT licence so anyone is free to use it as per that.

This is a work in progress and any advice is always welcome. This project is not finished yet and I will update this readme with usage instructions when I can.

### \yar\yar_autoloader

#### register

```
public static function register([Bool $prepend = false])
```

The autoload should be registered before anything else. This will add the yar directory to the path, add .php extentions to the extentions list if it is not already, and then register the spl_autoload standard function in the spl_autoload queue if it is not already.

| Type | Name | Optional | Default Value| Description |
| --- | --- | --- | --- | --- |
| Boolean | prepend | Yes | FALSE | Use this to prepend the spl_autoload function to the spl_autoload queue |

*This autoloader uses the standard spl_autoloader function. If you have already added the spl_autoloader standard function to the spl_autoload queue then a user notice will be thrown. If you are using the old __autoloader function then you will have to add your autoloader to the spl_autoload queue see [PHP spl_autoloar_register](http://php.net/manual/en/function.spl-autoload-register.php) for more information.*

```
require_once("path to yar dirautoloader.php");
\yar\yar_autoloader::register();
```

### \yar\yar

#### __construct

```
public function __construct([Bool $use_cache = true [, String $site = null] [, Bool $overwrite = false]]]);
```

Setup the yar router.

| Type | Name | Optional | Default Value| Description |
| --- | --- | --- | --- | --- |
| Boolean | use_cache | Yes | TRUE | Use the caching system |
| String | site | Yes | NULL | An identifier to prepend to the routing file name for use with multiple sites on one server |
| Boolean | overwrite | Yes | FALSE | Overwrites the current cached routing file |

#### __destruct

```
public function __construct()
```

Closes all handels and saves the routing file if use_cache was set.

#### add_routes_from_file

#### add_route

#### remove_route

#### find_route

### JSON route file

## \yar\file

### filename

### content

### __construct

### save

This is a file containing the routes to use. The format should be as follows:

```json
[
  {
    "route" : "URL route",
    "namespace" : "Namespace for controller class",
    "controller" : "Controller class name",
    "method" : "The method to call within the controller class"
  }
]
```

#### Example

```json
[
  {
    "route" : "home",
    "namespace" : "app",
    "controller" : "default_controller",
    "method" : "home"
  },
  {
    "route" : "about",
    "namespace" : "app",
    "controller" : "default_controller",
    "method" : "about"
  },
  {
    "route" : "blog",
    "namespace" : "app",
    "controller" : "blog_controller",
    "method" : "blog"
  },
  {
    "route" : "blog/{id:i}",
    "namespace" : "app",
    "controller" : "blog_controller",
    "method" : "blog"
  },
  {
    "route" : "blog/{id:i}/{title:s}",
    "namespace" : "app",
    "controller" : "blog_controller",
    "method" : "blog"
  },
]
```

This will add the following routes to the site:

* /home
* /about
* /blog
* /blog/{id:i}
* /blog/{id:i}/{title:s}

For 'slugs' use {name:type}. You can use any standart character for the name (RegEx search for names: ```([a-z|A-Z|0-9|-|_].*?)```) and types are as follows (The default is s if none is given):

| Name | Symbol | Description | Translated RegEx |
| --- | --- | --- | --- |
| Integer | i | Matches only an integer | ```([0-9|-|\+].*)``` |
| Decimal | d | Matches a decimal number (Float) | ```([0-9|-|\+|\.].*)``` |
| String | s | Matches a string | ```([a-z|A-Z|0-9|-|_|%|\+|\.].*)``` |

Slugs are translated into regex for searching and translated into 'params' within the ```\yar\yar->route``` array.
