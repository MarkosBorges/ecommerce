<?php 

use \helping\PageAdmin;
use \helping\Model\User;


$app->get('/admin/', function() { //chamar sem nenhum tipo de rota
  
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

	header("Location: /admin/"); // redireciona para a homepage da admin
	exit;
});

//Rota LOGOUT ==================================
$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login"); //redireciona para login screen
	exit;
});




//--------------------------- RECOVERY EMAIL ----------------------
$app->get("/admin/forgot", function(){

	$page = new PageAdmin([
		"header"=>false, // desabilitando o header padrão e o footer padrão 
		"footer"=>false
	]);
	$page->setTpl("forgot");


});

$app->post("/admin/forgot", function(){

	
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit();

});

$app->get("/admin/forgot/sent", function(){
	$page = new PageAdmin([
		"header"=>false, // desabilitando o header padrão e o footer padrão 
		"footer"=>false
	]);
	$page->setTpl("forgot-sent");

});
//------------------------ RESET ----------------------------------
$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]); //vrifica o code

	$page = new PageAdmin([
		"header"=>false, // desabilitando o header padrão e o footer padrão 
		"footer"=>false
	]);
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});




$app->post("/admin/forgot/reset", function(){ //

	$forgot = User::validForgotDecrypt($_POST["code"]);//verifica novamente o code

	User::setForgotUsed($forgot["idrecovery"]); //metod para não recuperar de novo, pois já foi usado

	$user = new User();
	$user->get((int)$forgot["iduser"]);
//-------------------
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

		"cost" =>12
	]);

//-------------------
	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false, // desabilitando o header padrão e o footer padrão 
		"footer"=>false
	]);
	$page->setTpl("forgot-reset-success");

});


 ?>