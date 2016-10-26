<?php

namespace FlSouto;

class Pipe{

	protected $filters = [];
	protected $fallback = null;

	function add($filter){
		if(!is_callable($filter)){
			throw new InvalidArgumentException("Pipe filter must be a callable.");
		}
		$this->filters[] = $filter;
		return $this;
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

}

class PipeResult{
	var $output = null;
	var $error = null;
}

