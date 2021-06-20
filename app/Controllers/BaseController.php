<?php

namespace EntityList\Controllers;

abstract class BaseController
{
	protected $requestType;

	abstract public function run();
}
