<?php
	// Include the YAR autoloader.
	require_once(__SYSTEM__ . DIRECTORY_SEPARATOR . "yar" . DIRECTORY_SEPARATOR . "yar" . DIRECTORY_SEPARATOR . "autoloader.php");
	
	// Load the YAR autoloader.
	yar_autoloader::register();
	
	// Add the controllers directorary to the include path.
	yar_autoloader::add_path(__DIR__ . DIRECTORY_SEPARATOR . "controllers");
	
	// Create a new YAR object.
	$router = new \yar\yar(true, null, true);
	
	// Add routes from a file.
	$router->add_routes_from_file(__SYSTEM__ . DIRECTORY_SEPARATOR . "routes.json");
	
	// Find the route to serve.
	$router->find_route("/home");
	
	// Render the page.
	$router->render();
?>
