<?php
namespace EntityList\Controllers;

use EntityList\App;

class ControllerFactory
{
	public static function makeController(string $controllerName,
										  string $requestType,
										  App $DIContainer)
	{
		$controller = null;

		switch ($controllerName) {
			case "HomeController":
				$controller = new HomeController($requestType);
				break;
			case "RegisterController":
				$controller = new RegisterController(
					$requestType,
					$DIContainer->get("entityDataGateway"),
					$DIContainer->get("entityValidator")
				);
				break;
		}

		return $controller;
	}
}
