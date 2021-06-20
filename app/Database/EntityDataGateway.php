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

	/**
	 * @param Entity $entity
	 */
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

	/**
	 * @param Entity $entity
	 */
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

	/**
	 * @param string $email
	 * @return int
	 */
	public function checkIfEmailExists(string $email): int
	{
		$statement = $this->pdo->prepare(
			"SELECT COUNT(*) FROM entitys WHERE email=?"
		);
		$statement->bindParam(1, $email, \PDO::PARAM_STR);
		$statement->execute();

		return (int)$statement->fetchColumn();
	}

	/**
	 * @return int
	 */
	public function countTableRows(): int
	{
		$statement = $this->pdo->prepare(
			"SELECT COUNT(*) FROM entitys"
		);
		$statement->execute();

		return (int)$statement->fetchColumn();
	}

	public function countSearchRows(string $keywords): int
	{
		$statement = $this->pdo->prepare(
			"SELECT COUNT(*) FROM entitys
                     WHERE CONCAT(`name`,' ',`surname`,' ',`group_number`,' ',`exam_score`)
                     LIKE :keywords"
		);
		$statement->bindValue("keywords", "%" . $keywords . "%");
		$statement->execute();

		return (int)$statement->fetchColumn();
	}

	public function getEntitys(int $offset, int $limit, string $orderBy, string $sort)
	{
		$sortingParams = $this->sanitizeSortingParams($orderBy, $sort);

		$statement = $this->pdo->prepare(
			"SELECT `name`, `surname`, `group_number`, `exam_score`
                     FROM `entitys`
                     ORDER BY {$sortingParams['orderBy']} {$sortingParams['sort']}
                     LIMIT :offset, :limit
          "
		);
		$statement->bindValue(":offset", $offset, \PDO::PARAM_INT);
		$statement->bindValue(":limit", $limit, \PDO::PARAM_INT);
		$statement->execute();

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function searchEntitys(string $keywords, int $offset, int $limit, string $orderBy, string $sort)
	{
		$sortingParams = $this->sanitizeSortingParams($orderBy, $sort);

		$statement = $this->pdo->prepare(
			"SELECT * FROM entitys
                     WHERE CONCAT(`name`,' ',`surname`,' ',`group_number`,' ',`exam_score`)
                     LIKE :keywords
                     ORDER BY {$sortingParams['orderBy']} {$sortingParams['sort']}
                     LIMIT :offset, :limit"
		);
		$statement->bindValue("keywords", "%" . $keywords . "%");
		$statement->bindValue(":offset", $offset, \PDO::PARAM_INT);
		$statement->bindValue(":limit", $limit, \PDO::PARAM_INT);
		$statement->execute();

		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

	private function sanitizeSortingParams(string $orderBy, string $sort)
	{
		$orderWhiteList = ["name", "surname", "group_number", "exam_score"];

		if (!in_array($orderBy, $orderWhiteList, true)) {
			$orderBy = "exam_score";
		}

		if ($sort !== "DESC" && $sort !== "ASC") {
			$sort = "DESC";
		}

		$sortingParams = array(
			"sort" => $sort,
			"orderBy" => $orderBy
		);

		return $sortingParams;
	}

	/**
	 * @param string $hash
	 * @return mixed
	 */
	public function getEntityByHash(string $hash)
	{
		$statement = $this->pdo->prepare(
			"SELECT * FROM entitys WHERE hash=?"
		);
		$statement->bindParam(1, $hash, \PDO::PARAM_STR);
		$statement->execute();

		return $statement->fetch(\PDO::FETCH_ASSOC);
	}
}
