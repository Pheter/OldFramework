<?php

namespace Seed;

class Framework {
    
    public function __construct() {
        
        try {
            $this->registerAutoload();
            $spyc = $this->getSpyc();
            $settings = $this->getSettings($spyc);
            $request = $this->getRequest();
            $plugins = $this->getPlugins($settings, $request);
            $router = $this->getRouter($spyc);
            list($request->resource, $request->path_parameters) = $router->route($request->path);
            $resource = $this->getResource($request, $plugins);
            
            echo $this->queryResource($resource, $request);
            
        } catch (\Exception $e) {
            
            require('ExceptionHandler.php');
            $handler = new ExceptionHandler($settings['debug']);
            $handler->render($e);
        }
    }
    
    
    private function registerAutoload() {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    
    private function getSpyc() {
        require('lib/spyc/spyc.php');
        return new \Spyc;
    }
    
    
    private function getSettings($spyc) {
        return $spyc->loadFile('config.yml');
    }
    
    
    private function getRequest() {
        require('Request.php');
        return new \Seed\Request;
    }
    
    
    private function getPlugins($settings, $request) {
        require('Plugins.php');
        return new \Seed\Plugins($settings, $request);
    }
    
    
    private function getRouter($spyc) {
        require('Router.php');
        return new \Seed\UrlRouter($spyc->loadFile('routes.yml'));
    }
    
    
    private function getResource($request, $plugins) {
        try {
            $class_name = 'Seed\Resources\\'.$request->resource;
            $method = $request->method;
            return new $class_name($plugins, $request->data);
        } catch (Exception $e) {
            throw new \Exception('The resourse: '.$request->resource.', does not exist.', null, 404);
        }
    }
    
    
    private function queryResource($resource, $request) {
        if (method_exists($resource, $request->method)) {
            return call_user_func_array(array($resource, $request->method), $request->path_parameters);
        } else {
            throw new \Exception('You cannot use: '.$request->method.', on the resourse: '.$request->resource.'.', null, 405);
        }
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
}
