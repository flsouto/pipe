<?php

namespace FlSouto;

class Pipe{

	protected $filters = [];
	protected $fallback = null;

	function __construct(array $filters=[]){
		if(!empty($filters)){
			$this->addArray($filters);
		}
	}

	function add($filter){
		if(!is_callable($filter)){
			throw new InvalidArgumentException("Pipe filter must be a callable.");
		}
		$this->filters[] = $filter;
		return $this;
	}

	function filters(){
		return $this->filters;
	}

	function fallback($value){
		$this->fallback = $value;
		return $this;
	}

	function run($input){
		$result = new PipeResult();
		$result->output = $input;
		foreach($this->filters as $filter){
			ob_start();
			$return = call_user_func($filter, $result->output);
			$error = ob_get_clean();
			if($error){
				$result->error = $error;
				$result->output = $this->fallback;
				break;
			}
			if(!is_null($return)){
				$result->output = $return;
			}
		}
		return $result;
	}

	function addArray(array $array){
		foreach($array as $filter){
			$this->add($filter);
		}
	}

	static function create(array $filters=[]){
		return new static($filters);
	}

}

class PipeResult{
	var $output = null;
	var $error = null;
}

