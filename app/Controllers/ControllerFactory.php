<?php
namespace EntityList\Controllers;

use EntityList\App;

class ControllerFactory
{
	public static function makeController(string $controllerName,
										  string $requestMethod,
										  string $action,
										  App $DIContainer)
	{
		$controller = null;

		switch ($controllerName) {
			case "HomeController":
				$controller = new HomeController($requestMethod);
				break;
			case "RegisterController":
				$controller = new RegisterController(
					$requestMethod,
					$DIContainer->get("entityDataGateway"),
					$DIContainer->get("entityValidator"),
					$DIContainer->get("util"),
					$DIContainer->get("authManager"),
					$DIContainer->get("urlManager")
				);
				break;
			case "ProfileController":
				$controller = new ProfileController(
					$requestMethod,
					$action,
					$DIContainer->get("entityDataGateway"),
					$DIContainer->get("entityValidator"),
					$DIContainer->get("authManager"),
					$DIContainer->get("util"),
					$DIContainer->get("urlManager")
				);
				break;
		}

		return $controller;
	}
}
