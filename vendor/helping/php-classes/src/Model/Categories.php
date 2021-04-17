<?php

namespace helping\Model;

use \helping\DB\Sql;
use \helping\Model;
use \helping\Mailer;

class Category extends Model{

	const SESSION = "User"; 
	const SECRET = "HelpingPhp_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";

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

	public static function listAll(){
		$sql = new Sql();
		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
	}

	public function save(){

		$sql = new Sql();
		/*
		desperson VARCHAR(64), 
		deslogin VARCHAR(64), 
		despassword VARCHAR(256), 
		desemail VARCHAR(128), 
		nrphone BIGINT, 
		inadmin TINYINT
		*/
		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()

		));

		$this->setData($results[0]);

	}
	public function get($iduser){
	 
		 $sql = new Sql();
		 
		 $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser;", array(
		 ":iduser"=>$iduser
		 ));
		 
		 $data = $results[0];
		 
		 $this->setData($data);
	 
	 }

	 public function update(){
	 	$sql = new Sql();
		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>$this->getdesperson(),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>$this->getdespassword(),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()

		));
		$this->setData($results[0]);
	 }

	 public function delete(){

	 	$sql = new Sql();
	 	$sql->query("CALL sp_users_delete(:iduser)", array(
	 		":iduser"=>$this->getiduser()

	 	));

	 }

	 public static function getForgot($email, $inadmin = true){

	 	$sql = new Sql();
	 	$results = $sql->select("
	 		SELECT *
	 		FROM tb_persons a
	 		INNER JOIN tb_users b USING(idperson)
	 		WHERE a.desemail = :email;", 
	 		array(
	 		":email"=>$email
	 	));

	 	if(count($results) === 0){
	 		throw new \Exception("Não foi possível recuperar a senha. <br><br> Verifique seu e-mail novamente ou o seu e-mail não está cadastrado.<br><br><hr>");
	 		
	 	}else{ //create a new registry of rec to passw

	 		$data = $results[0]; //get results on index 0
	 		$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
	 				":iduser"=>$data["iduser"], //valor de data na posição id user
	 				":desip"=>$_SERVER["REMOTE_ADDR"] //get a ip address
	 		));
	 		if(count($results2) === 0){

	 			throw new \Exception("Não foi possível recuperar a senha!");
	 			
	 		}else{

	 			$dataRecovery = $results2[0]; //guarda os dados que gerou

	 			// encrypt
				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

				if ($inadmin === true) {

					$link = "http://www.helpingcommerce.com.br/admin/forgot/reset?code=$code";

				} else {

					$link = "http://www.helpingcommerce.com.br/forgot/reset?code=$code";
					
				}

				$mailer = new Mailer($data["desemail"],$data['desperson'], "Redefinir senha da Helping Store", "forgot", array(
						"name"=>$data['desperson'],
						"link"=>$link

				));
				$mailer->send();

				return $data; //return of data user 

	 		}
	 	}
	 }

	 public static function validForgotDecrypt($code){

	 	base64_decode($code);
		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{

			return $results[0];

		}	 	


	 }

	 public static function setForgotUsed($idrecovery){
		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));

	 }

	 public function setPassword($password){

	 	$sql = new Sql();
	 	$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser", array(
	 		":password"=>$password,
	 		":iduser"=>$this->getiduser

	 	));
	 }
}


?>