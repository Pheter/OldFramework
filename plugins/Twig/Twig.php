<?php

namespace Seed\Plugin;

class Twig {
    
    private $Twig;
    
    public function __construct($settings) {
        
        require_once 'lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
        
        $loader = new \Twig_Loader_Filesystem('views');
        $this->Twig = new \Twig_Environment($loader,array(
                'cache' => 'cache/Twig')
        );
    }
    
    
    public function render($template, $variables = array()) {
        
        return call_user_func(array($this->Twig->loadTemplate($template), 'render'), $variables);
    }
}
