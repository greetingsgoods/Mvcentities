<?php
namespace EntityList\Database;

use EntityList\Entities\Entity;

class EntityDataGateway
{
	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * EntityDataGateway constructor.
	 * @param \PDO $pdo
	 */
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Inserts new Entity into `entitys` table
	 *
	 * @param Entity $entity
	 *
	 * @return void
	 */
	public function insertEntity(Entity $entity): void
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
	 * Updates a entity row in `entitys` table
	 *
	 * @param Entity $entity
	 *
	 * @return void
	 */
	public function updateEntity(Entity $entity): void
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
	 * Returns a number of records found containing $email
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public function checkIfEmailExists(string $email): bool
	{
		$statement = $this->pdo->prepare(
			"SELECT COUNT(*) FROM entitys WHERE email=?"
		);
		$statement->bindParam(1, $email, \PDO::PARAM_STR);
		$statement->execute();

		$rowCount = (int)$statement->fetchColumn();

		return $rowCount > 0 ? true : false;
	}

	/**
	 * Returns a number of rows in the `entitys` table
	 *
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

	/**
	 * Returns a number of rows containing $keywords
	 *
	 * @param string $keywords
	 *
	 * @return int
	 */
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

	/**
	 * Returns an array of entity rows
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderBy Field to order by
	 * @param string $sort Ordering direction
	 *
	 * @return array
	 */
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

	/**
	 * Returns an array of entity rows found by $keywords
	 *
	 * @param string $keywords String to search for
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderBy Field to order by
	 * @param string $sort Ordering direction
	 *
	 * @return array
	 */
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

	/**
	 * Makes sure that ordering parameters don't contain something apart from whitelisted values
	 *
	 * @param string $orderBy Field to order by
	 * @param string $sort Ordering direction
	 *
	 * @return array
	 */
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
	 * Returns a entity row containing $hash
	 *
	 * @param string $hash
	 *
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
