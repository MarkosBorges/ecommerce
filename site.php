<?php 

use \helping\Page;
use \helping\Model\Product;
use \helping\Model\Category;
use \helping\Model\Cart;

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

$app->get("/categories/:idcategory", function($idcategory){

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();
	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);
	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++) { 
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}

	$page = new Page();
	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);

});
// ---------------- product ---------------

$app->get("/products/:desurl", function($desurl){

	$product = new Product();
	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail",[
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()
	]);


});
// ------------- car -----------
$app->get("/cart", function(){
	$cart = Cart::getFromSession();
	
	$page = new Page();
	$page->setTpl("cart");


});

 ?>