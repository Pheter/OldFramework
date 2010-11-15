<?php

namespace Seed;

class Plugins {
    
    private $settings;
    private $plugins = array();
    
    public function __construct($settings) {
        
        $this->settings = $settings;
        
        if (property_exists($settings, 'plugins')) {
            $this->buildPlugins($settings->plugins);
        }
    }
    
    
    public function buildPlugins($plugins) {
        
        foreach($plugins as $plugin_name) {
            $class_name = 'Seed\Plugin\\'.$plugin_name;
            
            require("plugins/$plugin_name/$plugin_name.php");
            $this->plugins[$plugin_name] = new $class_name($this->settings);
        }
    }
}
