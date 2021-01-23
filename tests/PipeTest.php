<?php
use PHPUnit\Framework\TestCase;

#mdx:h autoload
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
    	#/mdx echo $result->output

        $this->assertEquals('fabio', $result->output);
    }

    function testValidation(){
    	#mdx:2
    	$pipe = new Pipe();
    	$pipe->add(function($value){
    		if(strstr($value,'4')){
    			echo 'The value cannot contain the number 4.';
    		}
    	});
    	$result = $pipe->run('f4b10');
    	#/mdx echo $result->error

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
    	#mdx:3
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
    	#/mdx
    	$this->assertEmpty($result->error);
    }

    function testFallbackIsReturnedOnError(){
    	#mdx:4
    	$pipe = new Pipe();
    	$pipe->fallback('default');
        $pipe->add(function($v){
            iF(preg_match("/\d/",$v)){
                echo "The value cannot contain digits.";
            }
        });
    	$result = $pipe->run('My name is 12345');
    	#/mdx echo $result->output
    	$this->assertEquals('default',$result->output);
    }

    function testFallbackWhenNull(){
        #mdx:4.1
        $pipe = new Pipe();
        $pipe->fallback('default');
        $result = $pipe->run(null);
        #/mdx echo $result->output
        $this->assertEquals('default',$result->output);
    }

    function testFallbackFailsOnEmptyString(){
        #mdx:4.2
        $pipe = new Pipe();
        $pipe->fallback('default');
        $result = $pipe->run('');
        #/mdx var_dump($result->output)
        $this->assertEquals('',$result->output);
    }

    function testFallbackWhenCustom(){
        #mdx:4.3
        $pipe = new Pipe();
        $pipe->fallback('default',[null,'',0]);
        $result = $pipe->run('');
        #/mdx echo $result->output
        $this->assertEquals('default',$result->output);
    }

    function testCreateWithArray(){
		#mdx:5
    	$pipe = Pipe::create([
    		'trim',
    		function($value){ return str_replace('_','/',$value); }
    	]);
    	#/mdx

    	$result = $pipe->run(' flsouto_pipe ');

    	$result->output .= ':'.count($pipe->filters());

    	$this->assertEquals('flsouto/pipe:2',$result->output);
    }

    function testFallbackDefaultsToInput(){

        #mdx:6
        $pipe = new Pipe();
        $pipe->add(function($v){
            iF(preg_match("/\d/",$v)){
                echo "The value cannot contain digits.";
            }
        });

        $result = $pipe->run($input="My name is 12345");
        #/mdx echo $result->output

        $this->assertEquals($input,$result->output);

    }

}
