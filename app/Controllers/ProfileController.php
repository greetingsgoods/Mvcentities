<?php
namespace EntityList\Controllers;

use EntityList\AuthManager;
use EntityList\Helpers\UrlManager;
use EntityList\Helpers\Util;
use EntityList\Database\EntityDataGateway;
use EntityList\Validators\EntityValidator;

class ProfileController extends BaseController
{
	private $gateway;
	private $validator;
	private $authManager;
	private $util;
	private $urlManager;

	public function __construct(string $requestMethod,
								string $action,
								EntityDataGateway $entityDataGateway,
								EntityValidator $entityValidator,
								AuthManager $authManager,
								Util $util,
								UrlManager $urlManager)
	{
		$this->requestMethod = $requestMethod;
		$this->action = $action;
		$this->gateway = $entityDataGateway;
		$this->validator = $entityValidator;
		$this->authManager = $authManager;
		$this->urlManager = $urlManager;
		$this->util = $util;
	}

	private function processGetRequest()
	{
		// Fetching entity data from the database and preparing it for passing into view
		$entityData = $this->gateway->getEntityByHash($_COOKIE["hash"]);
		$params["values"] = $entityData;
		$params["formAction"] = "edit";

		if ($this->action === "edit") {
			$this->render(__DIR__ . "/../../views/register.view.php", $params);
		} else {
			$this->render(__DIR__ . "/../../views/profile.view.php", $params);
		}
	}

	private function processPostRequest()
	{
		if ($this->action === "edit") {
			$values = $this->grabPostValues();
			$entity = $this->util->createEntity($values);
			$errors = $this->validator->validateAllFields($entity);
			$entity->setHash($_COOKIE["hash"]);

			if (empty($errors)) {
				$this->gateway->updateEntity($entity);
				$this->urlManager->redirect("/?notify=1");
			} else {
				// Re-render the form passing $errors and $values arrays
				$params["values"] = $values;
				$params["errors"] = $errors;
				$this->render(__DIR__ . "/../../views/register.view.php", $params);
			}
		}
	}

	private function grabPostValues()
	{
		$values = [];

		$values["name"] = array_key_exists("name", $_POST) ?
			strval(trim($_POST["name"])) :
			"";
		$values["surname"] = array_key_exists("surname", $_POST) ?
			strval(trim($_POST["surname"])) :
			"";
		$values["birth_year"] = array_key_exists("birth_year", $_POST) ?
			intval($_POST["birth_year"]) :
			0;
		$values["gender"] = array_key_exists("gender", $_POST) ?
			strval($_POST["gender"]) :
			"";
		$values["group_number"] = array_key_exists("group_number", $_POST) ?
			strval(trim($_POST["group_number"])) :
			"";
		$values["exam_score"] = array_key_exists("exam_score", $_POST) ?
			intval($_POST["exam_score"]) :
			0;
		$values["email"] = array_key_exists("email", $_POST) ?
			strval(trim($_POST["email"])) :
			"";
		$values["residence"] = array_key_exists("residence", $_POST) ?
			strval($_POST["residence"]) :
			"";

		return $values;
	}

	private function render($file, $params = [])
	{
		extract($params, EXTR_SKIP);
		return require_once "{$file}";
	}

	public function run()
	{
		// Check if user is logged in first
		if (!$this->authManager->checkIfAuthorized()) {
			// If he's not we redirect to the registration page
			$this->urlManager->redirect("/register");
		}

		if ($this->requestMethod === "GET") {
			$this->processGetRequest();
		} else {
			$this->processPostRequest();
		}
	}
}
