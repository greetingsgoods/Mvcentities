<?php
use EntityList\App;
use EntityList\Validators\EntityValidator;
use EntityList\Helpers\UrlManager;
use EntityList\Database\{Connection, EntityDataGateway};

$app = new App();

$app->bind("config", require_once "../config.php");
$app->bind("connection", (new Connection)->make($app->get("config")));
$app->bind("entityDataGateway", new EntityDataGateway($app->get("connection")));
$app->bind("entityValidator", new EntityValidator($app->get("entityDataGateway")));
$app->bind("urlManager", new UrlManager());


