<?php

namespace Seed;

class ExceptionHandler {
    
    private $debug;
    
    public function __construct($debug) {
        
        $this->debug = $debug;
    }
    
    public function render($exception) {
        
        $msg = $exception->getMessage();
        $line = $exception->getLine();
        $code = $exception->getCode() >= 1 ? $exception->getCode() : 500;
        
        header(true, null, $code);
        
        echo '<p>Caught an exception with the message: '.$msg.'</p>';
        echo '<p>On line: '.$line.'</p>';
        echo '<p>Status code is: '.$code.'</p>';
    }
}
