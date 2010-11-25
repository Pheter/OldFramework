<?php

//Include generic functions
require('functions/array_to_object.php');

//Enable autoload
spl_autoload_register(
    
    function($class_name) {
        
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

//Build Plugins
require('Plugins.php');
$Plugins = new Seed\Plugins($Settings);

//Initialise URL Router
require('Router.php');
$Router = new Seed\Router($Spyc->loadFile('routes.yml'));

//Route URL
$url = parse_url($_SERVER['REQUEST_URI']);
$request = $Router->route($url['path']);

//Instantiate resource
$class_name = 'Seed\Resources\\'.$request['resource'];
$verb = $_SERVER['REQUEST_METHOD'];
$Resource = new $class_name($Plugins);

//Apply verb to resource (aka. call the method of the resource eg. $resource->get())
if (method_exists($Resource, $verb)) {
    echo call_user_func_array(array($Resource, $verb), $request['parameters']);
} else {
    //TODO: Trigger 405 error message eg. throw new Seed\Exception\405Error;
}
