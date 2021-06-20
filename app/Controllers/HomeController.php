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
		$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
		$perPage = 10;
		$offset = $this->pager->calculatePositioning($page, $perPage);
		$entitys = $this->entityDataGateway->getEntitys($offset, $perPage);
		$columnCount = $this->entityDataGateway->countTableRows();
		$totalPages = $this->pager->calculateTotalPages($columnCount, $perPage);

		$params["totalPages"] = $totalPages;
		$params["entitys"] = $entitys;

		$this->render(__DIR__ . "/../../views/home.view.php", $params);
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
