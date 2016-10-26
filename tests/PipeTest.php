<?php
use PHPUnit\Framework\TestCase;

#mdx:h require
require_once('vendor/autoload.php');

#mdx:h use
use FlSouto\Pipe;

class PipeTest extends TestCase
{

    function testFilter(){

    	#mdx:1
    	$pipe = new Pipe();
    	$pipe->add('trim')->add(function($value){
    		return str_replace(['4','1','0'],['a','i','o'],$value);
    	});

    	$result = $pipe->run(' f4b10 ');
    	#/mdx $result->output

        $this->assertEquals('fabio', $result->output);
    }

    function testValidation(){
    	$pipe = new Pipe();
    	$pipe->add(function($value){
    		if(strstr($value,'4')){
    			echo 'The value cannot contain the number 4.';
    		}
    	});
    	
    	$result = $pipe->run('f4b10');

    	$this->assertEquals('The value cannot contain the number 4.', $result->error);
    }

    function testValidation2(){
		$pipe = new Pipe();
    	$pipe->add(function($value){
    		if(strstr($value,'4')){
    			echo 'The value cannot contain the number 4.';
    		}
    	});
    
    	$result = $pipe->run('fab10');
    	$this->assertEmpty($result->error);
    }

    function testValidationRunsAfterFilter(){
    	$pipe = new Pipe();
    	$pipe->add(function($value){
    		return str_replace('4','a',$value);
    	});
    	$pipe->add(function($value){
    		if(strstr($value,'4')){
    			echo 'The value cannot contain the number 4.';
    		}
    	});
    	$result = $pipe->run('f4b10');
    	$this->assertEmpty($result->error);
    }

    function testFallbackIsReturnedOnError(){
    	$pipe = new Pipe();
    	$pipe->fallback('default');
    	$pipe->add(function($value){
    		if(empty($value)){
    			echo 'The value cannot be blank.';
    		}
    	});
    	$result = $pipe->run('');
    	$this->assertEquals('default',$result->output);
    }

    function testCreateWithArray(){
    	
    	$pipe = Pipe::create([
    		'trim',
    		function($value){ return str_replace('_','/',$value); }
    	]);

    	$result = $pipe->run(' flsouto_pipe ');

    	$result->output .= ':'.count($pipe->filters());

    	$this->assertEquals('flsouto/pipe:2',$result->output);
    }

}
