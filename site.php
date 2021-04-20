<?php 

use \helping\Page;

$app->get('/', function() { //chamar sem nenhum tipo de rota
    
	//$sql = new helping\DB\Sql();

	//$results = $sql->select("SELECT * FROM tb_users");

	//echo json_encode($results);
	//var_dump($results);
	$page = new Page(); //cria nova página 
	$page->setTpl("index"); //carrega conteúdo html principal



});






 ?>