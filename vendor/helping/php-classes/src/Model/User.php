<?php

namespace helping\Model;

use \helping\DB\Sql;
use \helping\Model;

class User extends Model{

	const SESSION = "User"; 

	public static function login($login, $password){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array( //:LOGIN = já evita injection
			":LOGIN"=>$login

		));

		if(count($results)===0){
			throw new \Exception("USUÁRIO INEXISTENTE OU SENHA INVÁLIDA");	
		}

		$data = $results[0];

		if(password_verify($password, $data["despassword"]) === true){ //verifica a senha / hash que está no banco de dados
			$user = new User();
//			$user->setiduser($data["iduser"]);
			$user->setData($data); //metodo setData | p/ criar dinamicamente

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		}else{
			throw new \Exception("USUÁRIO INEXISTENTE OU SENHA INVÁLIDA");				
		}

	}

	//verifica se é um user admin ==================================
	public static function verifyLogin($inadmin = true){ 

		if(
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0 //se for maior que zero é um user
			||
			(bool)$_SESSION[User::SESSION]["inadmin"]  !== $inadmin
		){
			header("Location: /admin/login");
			exit();
		}
	}

	//Logout =========================================
	public static function logout(){
		$_SESSION[User::SESSION] = NULL;


	}
}


?>