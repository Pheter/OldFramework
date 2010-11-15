<?php

namespace Seed\Plugin;

class Twig {
    
    private $twig;
    
    public function __construct($settings) {
        
        require_once 'lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        
        $loader = new \Twig_Loader_Filesystem('views');
        $this->twig = new \Twig_Environment($loader,array(
                'cache' => 'cache/Twig')
        );
    }
    
    
    public function __call($name, $args) {
        
        return call_user_func_array(array($this->twig, $name), $args);
    }
    
    
    public function __get($name) {
        
        return $this->twig->$name;
    }
}
