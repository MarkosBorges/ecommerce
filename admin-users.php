<?php

use \helping\PageAdmin;
use \helping\Model\User;



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


?>