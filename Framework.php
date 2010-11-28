<?php

namespace Seed;

class Framework {
    
    public function __construct() {
        
        spl_autoload_register(array($this, 'autoload'));
        
        require('lib/spyc/spyc.php');
        $spyc = new \Spyc;
        
        $settings = $spyc->loadFile('config.yml');
        
        require('Request.php');
        $request = new \Seed\Request;
        
        require('Plugins.php');
        $plugins = new \Seed\Plugins($settings, $request);
        
        require('Router.php');
        $router = new \Seed\UrlRouter($spyc->loadFile('routes.yml'));
        //Route the request and populate the 'resource' and 'path_parameters' properties of the request object.
        list($request->resource, $request->path_parameters) = $router->route($request->path);
        
        try {
            $resource = $this->initResource($request, $plugins);
        } catch (Exception $e) {
            //TODO: Trigger 404 error.
        }
        
        if (method_exists($resource, $request->method)) {
            $response = call_user_func_array(array($resource, $request->method), $request->path_parameters);
        } else {
            //TODO: Trigger 405 error message.
        }
        
        echo $response;
    }
    
    
    private function autoload($class_name) {
        $class_name = explode('\\', $class_name);
        
        if (array_key_exists(2, $class_name)) {
            if ($class_name[0] === 'Seed') {
                $type = $class_name[1];
                $class_name = $class_name[2];
                
                require("$type/$class_name.php");
            }
        }
    }
    
    
    private function initResource($request, $plugins) {
        $class_name = 'Seed\Resources\\'.$request->resource;
        $method = $request->method;
        return new $class_name($plugins, $request->data);
    }
}
