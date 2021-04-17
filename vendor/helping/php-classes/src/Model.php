<?php 

namespace helping;

class Model{

	private $values = [];

	public function __call($name, $args){

		$method = substr($name, 0, 3);
		$fieldName = substr($name, 3, strlen($name));


		switch ($method) {
			case 'get':
				return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
				break;

			case 'set':
				$this->values[$fieldName] = $args[0]; // o rgs é o valor que foi passado para o atributo
				break;	

		}
	//	var_dump($method, $fieldName);
	//	exit;
	}

	public function setData($data = array()){
		foreach ($data as $key => $value) {
			$this->{"set".$key}($value);
			# code...
		}
	}

	public function getValues(){
		return $this->values;
	}
}


 ?>