<?php
namespace EntityList\Controllers;

abstract class BaseController
{
	protected $requestMethod;
	protected $action;

	abstract public function run();
}
