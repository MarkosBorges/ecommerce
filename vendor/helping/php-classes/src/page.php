<?php

namespace helping; //onde a classe está

use Rain\Tpl; // usar uma classe que está em outro namespace | do name Rain

class Page{

	private $tpl;
	private $options = [];
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]

	];

	public function __construct($opts = array(), $tpl_dir = "/views/"){

//		$this->defaults["data"]["session"] = $_SESSION;

		$this->options = array_merge($this->defaults, $opts); // pega as duas e mescla e guarda dentro do options

		$config = array(
		"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
		"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
		"debug"         => false // set to false to improve the speed
	   );

		Tpl::configure( $config );

		$this->tpl = new Tpl;

		$this->setData($this->options["data"]); //chamando o metodo setdata

/*		foreach ($this->options["data"] as $key => $value) {

			$this->tpl->assign($key, $value); //passando template | atribuindo vriaveis que vão apararecer no template | ex: vai pegar título e o valor
			# code...
		}
		// substituido pela function setData
*/

		if ($this->options["header"] === true) $this->tpl->draw("header");

	}

// =======================================================
	private function setData($data = array()){

		foreach ($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}	
	}

//	==================================================

	public function setTpl($name, $data = array(), $returnHTML = false){

		$this->setData($data);

		return $this->tpl->draw($name, $returnHTML); // passa o nome do template e o retuno do html
	}

	public function __destruct(){

		if ($this->options["footer"] === true) $this->tpl->draw("footer");

	}
}

?>