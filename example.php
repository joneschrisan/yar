<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . "controllers");

	require_once(__SYSTEM__ . DIRECTORY_SEPARATOR . "yar" . DIRECTORY_SEPARATOR . "yar" . DIRECTORY_SEPARATOR . "autoloader.php");
	yar_autoloader::register();
	
	$router = new \yar\yar(true, null, true);
	$router->add_routes_from_file(__SYSTEM__ . DIRECTORY_SEPARATOR . "routes.json");
	
	$router->find_route("/home");
	
	$router->render();
?>