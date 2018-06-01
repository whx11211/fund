<?php 

include_once 'common/init.php';

$class = $argv[1];
$method = $argv[2];


if ($class && class_exists($class.'Control')) {
    $class = $class.'Control';
    if (method_exists($class, $method)) {
        $control = new $class();
        $control->$method();
    }
    else {
        Output::fail(ErrorCode::METHOD_NOT_EXISTS);
    }
    
}
else {
    Output::fail(ErrorCode::CLASS_NOT_EXISTS);
}

