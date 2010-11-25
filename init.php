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

//Create $request array
$url = parse_url($_SERVER['REQUEST_URI']);
$request['path'] = $url['path'];
$request['method'] = strtoupper($_SERVER['REQUEST_METHOD']);

//HTML form fix (enable usage of methods other than GET and POST)
if ($request['method'] == 'POST') {
    if (isset($_POST['_method'])) {
        $request['method'] = strtoupper($_POST['_method']);
    }
}

switch ($request['method']) {
    case 'GET':
        $request['data'] = $_GET;
        break;
    default:
        parse_str(file_get_contents('php://input'), $request['data']);
        break;
}

unset($request['data']['_method']);

//Build Plugins
require('Plugins.php');
$Plugins = new Seed\Plugins($Settings);

//Initialise URL Router
require('Router.php');
$Router = new Seed\Router($Spyc->loadFile('routes.yml'));

//Route URL
$request = $Router->route($request['path']);

//Instantiate resource
try {
    $class_name = 'Seed\Resources\\'.$request['resource'];
    $verb = $_SERVER['REQUEST_METHOD'];
    $Resource = new $class_name($Plugins);
} catch (Exception $e) {
    //TODO: Trigger 404 error message.
}

//Apply verb to resource (aka. call the method of the resource eg. $resource->get())
if (method_exists($Resource, $verb)) {
    echo call_user_func_array(array($Resource, $verb), $request['parameters']);
} else {
    //TODO: Trigger 405 error message eg. throw new Seed\Exception\405Error;
}
