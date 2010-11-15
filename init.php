<?php

//Include generic functions
require('functions/array_to_object.php');

//Enable autoload
spl_autoload_register(
    
    function($class_name) {
        
        //Extra backspaces added because of PHP strings.
        //Actual regex is: ^Seed\\(?P<type>\w+)\\
        if (preg_match('/^Seed\\\\(?P<type>\w+)\\\\/', $class_name, $matches)) {
            
            //Actual regex is: ^Seed\\\w+\\
            $class_name = preg_replace('/^Seed\\\\\w+\\\\/', '', $class_name);
            
            switch ($matches['type']) {
                case 'Resources':
                    require('resources/'.$class_name.'.php');
                    break;
                case 'Models':
                    require('models/'.$class_name.'.php');
                    break;
                case 'Views':
                    require('views/'.$class_name.'.php');
                    break;
            }
        }
    }
);

//Enable parsing of YAML files
require('lib/spyc/spyc.php');
$Spyc = new Spyc;

//Load settings
$Settings = array_to_object($Spyc->loadFile('config.yml'));

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
