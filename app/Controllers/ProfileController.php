<?php
namespace EntityList\Controllers;

use EntityList\AuthManager;
use EntityList\Database\EntityDataGateway;

class ProfileController extends BaseController
{
	private $entityDataGateway;
	private $authManager;

	public function __construct(string $requestType,
								string $action,
								EntityDataGateway $entityDataGateway,
								AuthManager $authManager)
	{
		$this->requestType = $requestType;
		$this->action = $action;
		$this->entityDataGateway = $entityDataGateway;
		$this->authManager = $authManager;
	}

	private function processGetRequest()
	{
		// Check if user is logged in first
		if (!$this->authManager->checkIfIsAuthorized()) {
			// If he's not we redirect to the registration page
			header("Location: /register");
			die();
		}

		// Fetching entity data from the database and preparing it for passing into view
		$entityData = $this->entityDataGateway->getEntityByHash($_COOKIE["hash"]);
		$params["values"] = $entityData;

		if ($this->action === "edit") {
			$this->render(__DIR__ . "/../../views/register.view.php", $params);
		} else {
			$this->render(__DIR__ . "/../../views/profile.view.php", $params);
		}
	}

	private function processPostRequest()
	{

	}

	private function render($file, $params = [])
	{
		extract($params, EXTR_SKIP);
		return require_once "{$file}";
	}

	public function run()
	{
		if ($this->requestType === "GET") {
			$this->processGetRequest();
		} else {
			$this->processPostRequest();
		}
	}
}
