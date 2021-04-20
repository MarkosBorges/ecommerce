<?php 
session_start();
require_once("vendor/autoload.php"); // traz as dependências


//REST FULL API
//carrrega classes 
use \Slim\Slim; 
use \helping\Page;
use \helping\PageAdmin;
use \helping\Model\User;
use \helping\Model\Category;

$app = new Slim(); //rota | manda para algum lugar

$app->config('debug', true);

require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");


$app->run(); 

 ?>