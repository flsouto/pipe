<?php

namespace FlSouto;

class Pipe{

	protected $filters = [];
	protected $fallback = null;
	protected $fallback_when = [];

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

	function fallback($value,$when=[null]){
		$this->fallback = $value;
		$this->fallback_when = [];
		foreach($when as $v){
			if(!is_array($v) && ctype_digit("$v")){
				$this->fallback_when[] = "$v";
				$this->fallback_when[] = (int)$v;
			} else{
				$this->fallback_when[] = $v;
			}
		}
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
		if(!is_null($this->fallback) && in_array($result->output, $this->fallback_when,true)){
			$result->output = $this->fallback;
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

