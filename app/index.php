<?php
	session_start();
	require "conf.inc.php";

	function myAutoloader($class) {
		$class = $class .".class.php";

		// Si notre classe existe
		if ( file_exists("core/".$class) ) {
			include "core/".$class;
		} else if ( file_exists("models/".$class) ) {
			include "models/".$class;
		} 
	}

	// Appel notre autoloader quand on essaie d'instancier un objet
	spl_autoload_register('myAutoloader');

	//http://localhost/3IW%20classe%202/user/add
	//Récupérer user > définir dans une variable $c
	//Récupérer add > définir dans une variable $a

	// /3IW%20classe%202/user/add?id=2
	// variable super globale, recupere l'uri
	$uri = $_SERVER["REQUEST_URI"];
	
	// transforme en tableau en séparant grace a ?
	$uri = explode("?", $uri);

	// on garde ce qu'il y a après le dossier racine
	$uri = str_ireplace(DIRNAME, "/", urldecode($uri[0]));
	// $uri -> user/modify/pseudonyme/3

	// Explode en séparant avec /
	$uriExploded = explode(DS, $uri);

	unset($uriExploded[0]);
	$uriExploded = array_values($uriExploded);
	
	//Condition ternaire pour affecter la chaine "index"
	$c = empty( $uriExploded[0] ) ? "index" : $uriExploded[0];
	$a = empty( $uriExploded[1] ) ? "index" : $uriExploded[1];

	unset($uriExploded[0]);
	unset($uriExploded[1]);

	//$uriExploded[2]="pseudonyme"
	//$uriExploded[3]=3
	$uriExploded = array_values($uriExploded);
	//$uriExploded[0]="pseudonyme"
	//$uriExploded[1]=3

	//Controller : NameController
	$c = ucfirst(strtolower($c))."Controller";
	//Action : nameAction
	$a = strtolower($a)."Action";

	$params = [ "POST" => $_POST, "GET"=>$_GET, "URL"=>$uriExploded ];

	//Est ce que le controller existe
	if ( file_exists("controllers/".$c.".class.php") ) {
		include("controllers/".$c.".class.php");

		if ( class_exists($c) ) {
			$objC = new $c();

			if ( method_exists($objC, $a) ) {
				$objC->$a( $params );

			} else {
				die("L'action ".$a." n'existe pas");
			}
		} else {
			die("La classe ".$c." n'existe pas");
		}
	} else {
		die("Le controller ".$c." n'existe pas");
	}