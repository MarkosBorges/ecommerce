<?php 

require_once("vendor/autoload.php"); // traz as dependências

//carrrega classes 
use \Slim\Slim; 
use \helping\Page;


$app = new Slim(); //rota | manda para algum lugar

$app->config('debug', true);

$app->get('/', function() { //quando chamar sem nenhum tipo de rota
    
	//$sql = new helping\DB\Sql();

	//$results = $sql->select("SELECT * FROM tb_users");

	//echo json_encode($results);
	//var_dump($results);
	$page = new Page(); //cria nova página 
	$page->setTpl("index"); //carrega conteúdo html principal



});

$app->run(); 

 ?>