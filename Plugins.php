<?php

class Plugins {
    
    private $settings;
    private $request;
    private $plugins = array();
    
    public function __construct($settings, $request) {
        
        $this->settings = $settings;
        $this->request = $request;
        
        if (array_key_exists('plugins', $settings)) {
            $this->buildPlugins($settings['plugins']);
        }
    }
    
    
    public function buildPlugins($plugins) {
        
        foreach($plugins as $plugin_name) {
            $class_name = 'Plugins_'.$plugin_name;
            
            require("plugins/$plugin_name/$plugin_name.php");
            $this->plugins[$plugin_name] = new $class_name($this->settings, $this->request);
        }
    }
    
    
    public function __get($name) {
        
        return $this->plugins[$name];
    }
}
