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

//Rota LOGOUT ==================================
$app->get('/admin/logout', function(){
	User::logout();
	header("Location: /admin/login"); //redireciona para login screen
	exit;
});
// -------------------------------------------------
// ====================== * USERS * =====================
$app->get("/admin/users", function(){
	User::verifyLogin(); 
	$users = User::listAll();
	$page = new PageAdmin();
	$page->setTpl("users", array(
		"users"=>$users
	));

});
// -----------------------------------------------------
// ==================== USERS CREATE ======================
$app->get("/admin/users/create", function(){
	User::verifyLogin(); 
	$page = new PageAdmin();
	$page->setTpl("users-create");

});
// ------------- DELETE = o slim percebe o /delete e para no :iduser
$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit();
});
// ====================== END DELETE ==================
// ==================== USERS UPDATE ====================
$app->get("/admin/users/:iduser", function($iduser){
	User::verifyLogin(); 
   $user = new User();
 
   $user->get((int)$iduser);
 
   $page = new PageAdmin();
 
   $page ->setTpl("users-update", array(
        "user"=>$user->getValues()
    ));
});
/*
BACKUP of the USERS
$app->get("/admin/users/:iduser", function($iduser){
	User::verifyLogin(); 
	$page = new PageAdmin();
	$page->setTpl("users-update");
});
*/
//_____________________________________________________
//_____________________________________________________
$app->post("/admin/users/create", function(){
	User::verifyLogin();


	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; //se for definido é 1 senão 0
	$user->setData($_POST);
	$user->save();

//	var_dump($user); //teste

	header("Location: /admin/users");
	exit;
});


$app->post("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0; 
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit();
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


//------- Teste --------
$app->post("/admin/users/create", function () {
    User::verifyLogin();
    $user = new User();
    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [
        "cost"=>12
    ]);
    $user->setData($_POST);
    $user->save();
    header("Location: /admin/users");
    exit;
});
/*
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
*/

$app->get("/admin/categories", function(){
	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();
	$page->setTpl("categories",[
		'categories'=>$categories
	]);
});
//===================== Category ===========================

$app->get("/admin/categories/create", function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){
	User::verifyLogin();

	$category = new Category();
	$category->setData($_POST);
	$category->save();
	header('Location: /admin/categories');
	exit();

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){
	User::verifyLogin();

	$category = new Category();
	$category->get((int)$idcategory);
	$category->delete();

	header('Location: /admin/categories');
	exit();

});
//------------------ UPDATE --------------------
$app->get("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();

	$category = new Category();
	$category->get((int)$idcategory);
	$page = new PageAdmin();
	$page->setTpl("categories-update",[
		'category'=>$category->getValues()

	]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();

	$category = new Category();
	$category->get((int)$idcategory);
	$category->setData($_POST);
	$category->save();

	header('Location: /admin/categories');
	exit();	

});


$app->run(); 

 ?>