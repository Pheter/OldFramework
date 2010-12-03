<?php

class Plugins_Twig {
    
    private $Twig;
    
    public function __construct($settings) {
        
        require_once 'lib/Twig/Autoloader.php';
        Twig_Autoloader::register();
        
        $cache_dir = $settings['debug'] ? false : 'cache/Twig';
        
        $loader = new Twig_Loader_Filesystem('templates');
        $this->Twig = new Twig_Environment($loader,array(
                'cache' => $cache_dir)
        );
    }
    
    
    public function render($template, $variables = array()) {
        
        return call_user_func(array($this->Twig->loadTemplate($template), 'render'), $variables);
    }
}
