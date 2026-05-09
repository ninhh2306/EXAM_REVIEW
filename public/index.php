<?php
session_start();

// public/index.php
define('ROOT', dirname(__DIR__));
define('APP_URL', 'https://exam_review.test');

require_once ROOT . "/app/core/Router.php";
require_once ROOT . "/app/core/Controller.php";

$router = new Router();

// Nạp các route
// Nạp các route
require_once ROOT . "/routes/web.php";
require_once ROOT . "/routes/admin.php";

$router->resolve();