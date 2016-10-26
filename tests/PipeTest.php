<?php
use PHPUnit\Framework\TestCase;
require_once('vendor/autoload.php');

use FlSouto\Pipe;

class PipeTest extends TestCase
{

    function testFilter(){

    	$pipe = new Pipe();
    	$pipe->add('trim')->add(function($value){
    		return str_replace(['4','1','0'],['a','i','o'],$value);
    	});

    	$result = $pipe->run(' f4b10 ');

        $this->assertEquals('fabio', $result->output);
    }

    CONST VALIDATION_ERR = 'The value cannot contain the number 4.';
    function addValidation(Pipe $pipe){
    	$pipe->add(function($value){
    		if(strstr($value,'4')){
    			echo self::VALIDATION_ERR;
    		}
    	});
    }

    function testValidation(){
    	$pipe = new Pipe();
    	$this->addValidation($pipe);
    	$result = $pipe->run('f4b10');
    	$this->assertEquals(self::VALIDATION_ERR, $result->error);
    }

    function testValidation2(){
    	$pipe = new Pipe();
    	$this->addValidation($pipe);
    	$result = $pipe->run('fab10');
    	$this->assertEmpty($result->error);
    }

    function testValidationRunsAfterFilter(){
    	$pipe = new Pipe();
    	$pipe->add(function($value){
    		return str_replace('4','a',$value);
    	});
    	$this->addValidation($pipe);
    	$result = $pipe->run('f4b10');
    	$this->assertEmpty($result->error);
    }

    function testFallbackIsReturnedOnError(){
    	$pipe = new Pipe();
    	$pipe->fallback('default');
    	$this->addValidation($pipe);
    	$result = $pipe->run('f4b10');
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
