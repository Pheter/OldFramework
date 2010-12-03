<?php

class SeedFramework {
    
    public function __construct() {
        
        try {
            $this->registerErrorHandler();
            $this->registerAutoload();
            $spyc = $this->getSpyc();
            $settings = $this->getSettings($spyc);
            $request = $this->getRequest();
            $plugins = $this->getPlugins($settings, $request);
            $router = $this->getRouter($spyc);
            list($request->resource, $request->path_parameters) = $router->route($request->path);
            $resource = $this->getResource($request, $plugins);
            echo $this->queryResource($resource, $request);
            
        } catch (Exception $e) {
            
            require('ExceptionHandler.php');
            $handler = new ExceptionHandler($settings['debug']);
            $handler->handle($e);
        }
    }
    
    
    private function registerErrorHandler() {
        set_error_handler(array($this, 'exceptionErrorHandler'));
    }
    
    
    private function registerAutoload() {
        spl_autoload_register(array($this, 'autoload'));
    }
    
    
    private function getSpyc() {
        require('lib/spyc/spyc.php');
        return new Spyc;
    }
    
    
    private function getSettings($spyc) {
        return $spyc->loadFile('config.yml');
    }
    
    
    private function getRequest() {
        require('Request.php');
        return new Request;
    }
    
    
    private function getPlugins($settings, $request) {
        require('Plugins.php');
        return new Plugins($settings, $request);
    }
    
    
    private function getRouter($spyc) {
        require('Router.php');
        return new Router($spyc->loadFile('routes.yml'));
    }
    
    
    private function getResource($request, $plugins) {
        $class_name = 'Resources_'.$request->resource;
        return new $class_name($plugins, $request->data);
    }
    
    
    private function queryResource($resource, $request) {
        if (method_exists($resource, $request->method)) {
            return call_user_func_array(array($resource, $request->method), $request->path_parameters);
        } else {
            throw new Exception('You cannot '.$request->method.' the resource '.$request->resource.'.', 405);
        }
    }
    
    
    private function exceptionErrorHandler($id, $msg, $file, $line) {
        
        if ($id == 2) {
            return;
        }
        
        throw new ErrorException($msg, 500, $id, $file, $line);
    }
    
    
    private function autoload($class_name) {
        $class_name = explode('_', $class_name);
        
        if (array_key_exists(1, $class_name)) {
            $type = strtolower($class_name[0]);
            $class_name = $class_name[1];
            
            include("$type/$class_name.php");
        }
    }
}
