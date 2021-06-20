<?php
namespace EntityList\Controllers;

use EntityList\App;

class ControllerFactory
{
	public static function makeController(string $controllerName,
										  string $requestType,
										  string $action,
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
					$DIContainer->get("entityValidator"),
					$DIContainer->get("util"),
					$DIContainer->get("authManager")
				);
				break;
			case "ProfileController":
				$controller = new ProfileController(
					$requestType,
					$action,
					$DIContainer->get("entityDataGateway"),
					$DIContainer->get("authManager")
				);
				break;
		}

		return $controller;
	}
}
