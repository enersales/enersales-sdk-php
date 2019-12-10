<?php

namespace Enersales\Examples;
require 'Example.php';

$test = new ExampleUnit();

if( isset($argv[1]) && method_exists($test, $argv[1]) ){
	$test->{$argv[1]}();
}else{
	if(!isset($argv[1])){
		echo "\nYou should specify a method to test.";	
	}else{
		echo "\nTest {$argv[1]} don't exist.";	
	}
	$testMethods = get_class_methods($test);
	echo "\nYou can test one of these methods: ";
	
	foreach($testMethods as $k=>$methodName){
		echo "\n {$k}.  {$methodName}";
	}
	echo "\n\n";
}




