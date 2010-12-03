<?php

class ExceptionHandler {
    
    private $debug;
    
    public function __construct($debug = true) {
        
        $this->debug = $debug;
    }
    
    
    public function handle($exception) {
        $msg = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $code = $exception->getCode() >= 1 ? $exception->getCode() : 500;
        
        header(true, null, $code);
        if ($this->debug) {
            $this->renderDetailed($msg, $file, $line, $code);
        } else {
            $this->renderSimple($msg, $file, $line, $code);
        }
    }
    
    
    private function renderDetailed($msg, $file, $line, $code) {
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Exception thrown: $msg</title>
        </head>
        <body>
            <p>Exception: <strong>$msg</strong></p>
            <p>In file: <strong>$file</strong></p>
            <p>On line: <strong>$line</strong></p>
            <p>Status code is: <strong>$code</strong></p>
        </body>
        </html>
        ";
    }
    
    
    private function renderSimple($msg, $file, $line, $code) {
        echo "<p>Error: <b>$code</b>";
    }
}
