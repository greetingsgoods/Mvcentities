<?php

use EntityList\App;
use EntityList\Database\{Connection, EntityDataGateway};

$app = new App();

$app->bind("config", require_once "../config.php");
$app->bind("connection", (new Connection)->make($app->get("config")));
$app->bind("entityDataGateway", new EntityDataGateway($app->get("connection")));


