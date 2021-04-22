<?php 

use \helping\Page;
use \helping\Model\Product;

$app->get('/', function() { //chamar sem nenhum tipo de rota
    
    $products = Product::listAll();
	//$sql = new helping\DB\Sql();

	//$results = $sql->select("SELECT * FROM tb_users");

	//echo json_encode($results);
	//var_dump($results);
	$page = new Page(); //cria nova página 
	$page->setTpl("index",[
		'products'=>Product::checkList($products)
	]); //carrega conteúdo html principal



});






 ?>