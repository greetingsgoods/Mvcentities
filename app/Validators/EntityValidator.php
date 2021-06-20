<?php
namespace EntityList\Validators;

use EntityList\AuthManager;
use EntityList\Entities\Entity;
use EntityList\Database\EntityDataGateway;

class EntityValidator
{
	/**
	 * @var EntityDataGateway
	 */
	private $entityDataGateway;

	/**
	 * @var AuthManager
	 */
	private $authManager;

	/**
	 * EntityValidator constructor.
	 * @param EntityDataGateway $entityDataGateway
	 * @param AuthManager $authManager
	 */
	public function __construct(EntityDataGateway $entityDataGateway, AuthManager $authManager)
	{
		$this->entityDataGateway = $entityDataGateway;
		$this->authManager = $authManager;

	}

	/**
	 * Returns an array of validation errors
	 *
	 * @param Entity $entity
	 *
	 * @return array
	 */
	public function validateAllFields(Entity $entity)
	{
		$errors = array();

		$errors["name"] = $this->validateName($entity->getName());
		$errors["surname"] = $this->validateSurname($entity->getSurname());
		$errors["group_number"] = $this->validateGroupNumber($entity->getGroupNumber());
		$errors["email"] = $this->validateEmail($entity->getEmail());
		$errors["exam_score"] = $this->validateExamScore($entity->getExamScore());
		$errors["birth_year"] = $this->validateBirthYear($entity->getBirthYear());
		$errors["gender"] = $this->validateGender($entity->getGender());
		$errors["residence"] = $this->validateResidence($entity->getResidence());

		// Removing correct fields from the errors array
		return array_filter($errors, function ($value) {
			return $value !== true;
		});
	}

	/**
	 * @param string $name
	 *
	 * @return bool|string
	 */
	private function validateName(string $name)
	{
		$nameLength = mb_strlen($name);
		// Pattern for name validation
		$pattern = "/^[А-ЯЁ]([\s\-\']?[а-яёА-ЯЁ][\s\-\']?)*$/u";

		if ($nameLength === 0) {
			return "Вы не заполнили обязательное поле \"Имя\".";
		} elseif ($nameLength > 50) {
			return "Имя не должно содержать более 50 символов, а Вы ввели {$nameLength}.";
		} elseif (!(preg_match($pattern, $name))) {
			return "Имя должно содержать только русские буквы и начинаться с заглавной буквы.";
		}

		return true;
	}

	/**
	 * @param string $surname
	 *
	 * @return bool|string
	 */
	private function validateSurname(string $surname)
	{
		$surnameLength = mb_strlen($surname);
		// Pattern for surname validation
		$pattern = "/^[А-ЯЁ]([\s\-\']?[а-яёА-ЯЁ][\s\-\']?)*$/u";

		if ($surnameLength === 0) {
			return "Вы не заполнили обязательное поле \"Фамилия\".";
		} elseif ($surnameLength > 50) {
			return "Фамилия не должна содержать более 50 символов, а Вы ввели {$surnameLength}.";
		} elseif (!(preg_match($pattern, $surname))) {
			return "Фамилия должна содержать только русские буквы и начинаться с заглавной буквы.";
		}

		return true;
	}

	/**
	 * @param string $gender
	 *
	 * @return bool|string
	 */
	private function validateGender(string $gender)
	{
		if ($gender !== Entity::GENDER_MALE && $gender !== Entity::GENDER_FEMALE) {
			return "Вы не выбрали свой пол.";
		}

		return true;
	}

	/**
	 * @param string $groupNumber
	 *
	 * @return bool|string
	 */
	private function validateGroupNumber(string $groupNumber)
	{
		$groupNumberLength = mb_strlen($groupNumber);
		// Pattern for group number validation
		$pattern = "/^[а-яёА-ЯЁ0-9]+$/u";

		if ($groupNumberLength === 0) {
			return "Вы не заполнили обязательное поле \"Номер группы\"";
		} elseif ($groupNumberLength < 2 || $groupNumberLength > 5) {
			return "Количество символов в номере группы должно находиться
                    в интервале от 2 до 5, а Вы ввели {$groupNumberLength}.";
		} elseif (!(preg_match($pattern, $groupNumber))) {
			return "Номер группы может содержать только цифры и русские буквы.";
		}

		return true;
	}

	/**
	 * @param int $examScore
	 *
	 * @return bool|string
	 */
	private function validateExamScore(int $examScore)
	{
		if ($examScore < 50 || $examScore > 300) {
			return "Баллы ЕГЭ должны находиться в интервале от 50 до 300 включительно.";
		}
		return true;
	}

	/**
	 * @param string $email
	 *
	 * @return bool|string
	 */
	private function validateEmail(string $email)
	{
		$isAuth = $this->authManager->checkIfAuthorized();

		if (mb_strlen($email) === 0) {
			return "Вы не заполнили обязательное поле \"E-mail\".";
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			// Validating email with the built-in function "filter_var"
			return "E-mail должен быть в формате \"example@domain.com\".";
		}

		if ($this->entityDataGateway->checkIfEmailExists($email)) {
			return $isAuth ? $this->validateEmailIfAuth($email) :
				"Такой e-mail уже существует.";
		}

		return true;
	}

	/**
	 * Validates e-mail if user is editing his/her profile
	 *
	 * @param string $email
	 * @return bool|string
	 */
	private function validateEmailIfAuth(string $email)
	{
		$currentEntityEmail = $this->entityDataGateway->getEntityByHash(
			$this->authManager->getHash()
		)["email"];

		return ($email === $currentEntityEmail) ? true : "Такой e-mail уже существует";
	}

	/**
	 * @param int $birthYear
	 *
	 * @return bool|string
	 */
	private function validateBirthYear(int $birthYear)
	{
		if ($birthYear < 1910 || $birthYear > 2008) {
			return "Год рождения должен находиться в интервале от 1910 до 2008 включительно.";
		}

		return true;
	}

	/**
	 * @param string $residence
	 *
	 * @return bool|string
	 */
	private function validateResidence(string $residence)
	{
		if ($residence !== Entity::RESIDENCE_RESIDENT &&
			$residence !== Entity::RESIDENCE_NONRESIDENT) {
			return "Вы не выбрали свое местоположение.";
		}

		return true;
	}
}
