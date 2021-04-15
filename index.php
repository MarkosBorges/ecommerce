<?php 
session_start();
require_once("vendor/autoload.php"); // traz as dependências


//REST FULL API
//carrrega classes 
use \Slim\Slim; 
use \helping\Page;
use \helping\PageAdmin;
use \helping\Model\User;

$app = new Slim(); //rota | manda para algum lugar

$app->config('debug', true);

$app->get('/', function() { //chamar sem nenhum tipo de rota
    
	//$sql = new helping\DB\Sql();

	//$results = $sql->select("SELECT * FROM tb_users");

	//echo json_encode($results);
	//var_dump($results);
	$page = new Page(); //cria nova página 
	$page->setTpl("index"); //carrega conteúdo html principal



});

$app->get('/admin', function() { //chamar sem nenhum tipo de rota
  
  	User::verifyLogin();  
	//$sql = new helping\DB\Sql();

	//$results = $sql->select("SELECT * FROM tb_users");

	//echo json_encode($results);
	//var_dump($results);
	$page = new PageAdmin(); //cria nova página 
	$page->setTpl("index"); //carrega conteúdo html principal



});

$app->get('/admin/login', function(){

	$page = new PageAdmin([
		"header"=>false, // desabilitando o header padrão e o footer padrão 
		"footer"=>false
	]);
	$page->setTpl("login");

});

$app->post('/admin/login', function(){ // rota post para login

	User::login($_POST["login"], $_POST["password"]); //valida o login

	header("Location: /admin"); // redireciona para a homepage da admin
	exit;
});

//Rota LOGOUT
$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login"); //redireciona para login screen
	exit;
});
$app->run(); 

 ?>