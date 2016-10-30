# Pipe

## Overview

This library exposes a simple API that allows you to apply a chain of filters and/or validators against arbitrary data.

## Installation

Through composer:

```
composer require flsouto/pipe
```

## Usage

The following example adds a 'trim' filter and a custom filter which replaces 4, 1 and 0 with a,i and o:

```php
require_once('vendor/autoload.php');
use FlSouto\Pipe;

$pipe = new Pipe();
$pipe->add('trim')->add(function($value){
	return str_replace(['4','1','0'],['a','i','o'],$value);
});

$result = $pipe->run(' f4b10 ');

echo $result->output;
```

The above code will output:

```
fabio
```

So all your filter function has to do is to accept a value, modify and return it.

## Validation

The next example uses the same API but this time makes sure the input value doesn't have a number '4'.

```php
use FlSouto\Pipe;

$pipe = new Pipe();
$pipe->add(function($value){
	if(strstr($value,'4')){
		echo 'The value cannot contain the number 4.';
	}
});    	
$result = $pipe->run('f4b10');

echo $result->error;
```

The above snippet outputs:

```
The value cannot contain the number 4.
```

So, if your filter *prints* something out, then it is considered to be a ***validator*** instead of a regular filter. The printed error message will be available in the `$result->error` property.

## Using Filters and Validation Together

In the following code snippet you have a filter happening before the validation, so the validation does not failed.

```php

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


```

This is just to ilustrate that filters and validators can work together. Notice the order they are applied is the same order they are added to the pipe.

## Defining a Fallback

The fallback method allows you to define a default value to be returned in case any error occurs:

```php

$pipe = new Pipe();
$pipe->fallback('default');
$pipe->add(function($value){
	if(empty($value)){
		echo 'The value cannot be blank.';
	}
});
$result = $pipe->run('');

echo $result->output;
```

The output will be:

```
default
```

## Alternative syntax

You can use the *addArray* method to add an array of filters at once or you can instantiate the Pipe class through the *create* method which accepts an array of filters too:

```php

  	$pipe = Pipe::create([
  		'trim',
  		function($value){ return str_replace('_','/',$value); }
  	]);


```

## Final Thoughts

There are no final thoughts - this is just the beginning (: