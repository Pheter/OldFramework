<?php

//Enable autoload
spl_autoload_register(function($class_name) {
        
        $class_name = explode('\\', $class_name);
        
        if ($class_name[0] === 'Seed') {
            $type = $class_name[1];
            $class_name = $class_name[2];
            
            require("$type/$class_name.php");
        }
    }
);

//Enable parsing of YAML files
require('lib/spyc/spyc.php');
$Spyc = new Spyc;

//Load settings
$Settings = $Spyc->loadFile('config.yml');

//Load Request object
require('request.php');
$Request = new Request;

//Build Plugins
require('Plugins.php');
$Plugins = new Seed\Plugins($Settings, $Request);

//Initialise URL Router
require('Router.php');
$Router = new Seed\Router($Spyc->loadFile('routes.yml'));

//Route URL to identify resource and URI parameters
list($Request->resource, $Request->path_parameters) = $Router->route($Request->path);

//Instantiate resource
try {
    $class_name = 'Seed\Resources\\'.$Request->resource;
    $method = $Request->method;
    $Resource = new $class_name($Plugins, $Request->data);
} catch (Exception $e) {
    //TODO: Trigger 404 error message.
}

//Call the method of the resource eg. $resource->get())
if (method_exists($Resource, $method)) {
    echo call_user_func_array(array($Resource, $method), $Request->path_parameters);
} else {
    //TODO: Trigger 405 error message eg. throw new Seed\Exception\405Error;
}
