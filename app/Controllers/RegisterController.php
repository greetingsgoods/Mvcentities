<?php
namespace EntityList\Controllers;

use EntityList\AuthManager;
use EntityList\Entities\Entity;
use EntityList\Database\EntityDataGateway;
use EntityList\Validators\EntityValidator;
use EntityList\Helpers\Util;


class RegisterController extends BaseController
{
	private $gateway;
	private $validator;
	private $util;
	private $authManager;

	public function __construct(string $requestType,
								EntityDataGateway $gateway,
								EntityValidator $validator,
								Util $util,
								AuthManager $authManager)
	{
		$this->requestType = $requestType;
		$this->gateway = $gateway;
		$this->validator = $validator;
		$this->util = $util;
		$this->authManager = $authManager;
	}

	private function processGetRequest()
	{
		$this->render(__DIR__ . "/../../views/register.view.php");
	}

	private function processPostRequest()
	{
		$values = $this->grabPostValues();
		$entity = $this->createEntity($values);
		$errors = $this->validator->validateAllFields($entity);

		if (empty($errors)) {
			$hash = $this->util->generateHash();
			$entity->setHash($hash);
			$this->gateway->insertEntity($entity);
			$this->authManager->logIn($hash);
			echo "Успех!";
		} else {
			// Re-render the form passing $errors and $values arrays
			$params["values"] = $values;
			$params["errors"] = $errors;
			$this->render(__DIR__ . "/../../views/register.view.php", $params);
		}

	}

	private function createEntity(array $values)
	{
		$entity = new Entity(
			$values["name"],
			$values["surname"],
			$values["group_number"],
			$values["email"],
			$values["exam_score"],
			$values["birth_year"],
			$values["gender"],
			$values["residence"]
		);

		return $entity;
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
		if ($this->requestType === "GET") {
			$this->processGetRequest();
		} else {
			$this->processPostRequest();
		}
	}
}

