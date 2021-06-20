<?php
namespace EntityList\Helpers;

use EntityList\Entities\Entity;

class Util
{
	public function generateHash(int $length = 32)
	{
		if ($length <= 8) {
			$length = 32;
		}
		return bin2hex(random_bytes($length));
	}

	public function createEntity(array $values)
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
}
