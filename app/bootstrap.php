<?php
use EntityList\{App, AuthManager};
use EntityList\Validators\EntityValidator;
use EntityList\Helpers\{UrlManager, Util};
use EntityList\Database\{Connection, EntityDataGateway};

$app = new App();

$app->bind("config", require_once "../config.php");
$app->bind("connection", (new Connection)->make($app->get("config")));
$app->bind("authManager", new AuthManager());
$app->bind("entityDataGateway", new EntityDataGateway($app->get("connection")));
$app->bind("entityValidator", new EntityValidator(
	$app->get("entityDataGateway"),
	$app->get("authManager")
));
$app->bind("urlManager", new UrlManager());
$app->bind("util", new Util());



