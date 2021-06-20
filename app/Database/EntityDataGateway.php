<?php
namespace EntityList\Database;

use EntityList\Entities\Entity;

class EntityDataGateway
{
	private $pdo;

	// Getting pdo object to work with
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function insertEntity(Entity $entity)
	{
		$statement = $this->pdo->prepare(
			"INSERT INTO entitys(first_name, surname, gender, group_number, 
                                            email, exam_score, birth_year, residence)
                       VALUES (:name, :sname, :gender, :groupnum, :email, :examscore, :byear, :residence)"
		);
		$statement->execute(array(
			"name" => $entity->getName(),
			"sname" => $entity->getSurname(),
			"gender" => $entity->getGender(),
			"groupnum" => $entity->getGroupNumber(),
			"email" => $entity->getEmail(),
			"examscore" => $entity->getExamScore(),
			"byear" => $entity->getBirthYear(),
			"residence" => $entity->getResidence()
		));
	}

	public function getEntityByEmail(string $email)
	{
		$statement = $this->pdo->prepare(
			"SELECT * FROM entitys WHERE email=?"
		);
		$statement->bindParam(1, $email, \PDO::PARAM_STR);
		$statement->execute();
		$row = $statement->fetch(\PDO::FETCH_ASSOC);

		return $row;
	}
}
