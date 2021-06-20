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
			"INSERT INTO entitys(name, surname, gender, group_number, 
                                            email, exam_score, birth_year, residence, hash)
                       VALUES (:name, :sname, :gender, :groupnum, :email, :examscore, :byear, :residence, :hash)"
		);
		$statement->execute(array(
			"name" => $entity->getName(),
			"sname" => $entity->getSurname(),
			"gender" => $entity->getGender(),
			"groupnum" => $entity->getGroupNumber(),
			"email" => $entity->getEmail(),
			"examscore" => $entity->getExamScore(),
			"byear" => $entity->getBirthYear(),
			"residence" => $entity->getResidence(),
			"hash" => $entity->getHash()
		));
	}

	public function updateEntity(Entity $entity)
	{
		$statement = $this->pdo->prepare(
			"UPDATE entitys 
                     SET `name` = :name,
                         `surname` = :sname,
                         `gender` = :gender,
                         `group_number` = :groupnum,
                         `email` = :email,
                         `exam_score` = :examscore,
                         `birth_year` = :byear,
                         `residence` = :residence
                     WHERE `hash` = :hash"
		);
		$statement->execute(array(
			"name" => $entity->getName(),
			"sname" => $entity->getSurname(),
			"gender" => $entity->getGender(),
			"groupnum" => $entity->getGroupNumber(),
			"email" => $entity->getEmail(),
			"examscore" => $entity->getExamScore(),
			"byear" => $entity->getBirthYear(),
			"residence" => $entity->getResidence(),
			"hash" => $entity->getHash()
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

	public function getEntityByHash(string $hash)
	{
		$statement = $this->pdo->prepare(
			"SELECT * FROM entitys WHERE hash=?"
		);
		$statement->bindParam(1, $hash, \PDO::PARAM_STR);
		$statement->execute();
		$row = $statement->fetch(\PDO::FETCH_ASSOC);

		return $row;
	}
}
