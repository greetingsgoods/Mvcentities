<?php
namespace EntityList\Controllers;

use EntityList\Database\EntityDataGateway;
use EntityList\Helpers\Pager;

class HomeController extends BaseController
{
	private $pager;
	private $entityDataGateway;

	public function __construct(string $requestMethod,
								Pager $pager,
								EntityDataGateway $entityDataGateway)
	{
		$this->requestMethod = $requestMethod;
		$this->pager = $pager;
		$this->entityDataGateway = $entityDataGateway;
	}

	private function processGetRequest()
	{
		$pagination = $this->getPaginationInfo();
		$order = $pagination["order"];
		$direction = $pagination["direction"];

		if (!isset($_GET["search"])) {
			$search = null;
			$entitys = $this->entityDataGateway->getEntitys(
				$pagination["offset"],
				$pagination["perPage"],
				$pagination["order"],
				$pagination["direction"]
			);
			$rowCount = $this->entityDataGateway->countTableRows();
			$totalPages = $this->pager->calculateTotalPages($rowCount, $pagination["perPage"]);

			$params = compact("totalPages", "entitys", "order", "direction", "search");

			$this->render(__DIR__ . "/../../views/home.view.php", $params);
		} else {
			$search = $_GET["search"];
			$entitys = $this->entityDataGateway->searchEntitys(
				$search,
				$pagination["offset"],
				$pagination["perPage"],
				$pagination["order"],
				$pagination["direction"]

			);
			$rowCount = $this->entityDataGateway->countSearchRows($search);
			$totalPages = $this->pager->calculateTotalPages($rowCount, $pagination["perPage"]);

			$params = compact("search", "order", "direction", "totalPages", "entitys");

			$this->render(__DIR__ . "/../../views/home.view.php", $params);
		}
	}

	/**
	 * @return array
	 */
	private function getPaginationInfo(): array
	{
		$pagination["perPage"] = 10;
		$pagination["page"] = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
		$pagination["offset"] = $this->pager->calculatePositioning(
			$pagination["page"],
			$pagination["perPage"]
		);
		$pagination["order"] = isset($_GET["order"]) ? strval($_GET["order"]) : "exam_score";
		$pagination["direction"] = isset($_GET["direction"]) ? strval($_GET["direction"]) : "DESC";

		return $pagination;
	}

	private function render($file, $params = [])
	{
		extract($params, EXTR_SKIP);
		return require_once "{$file}";
	}

	public function run()
	{
		if ($this->requestMethod === "GET") {
			$this->processGetRequest();
		}
	}
}
