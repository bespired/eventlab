<?php

if (php_sapi_name() !== 'cli') {
    // If it's accessed via web, return a 403 Forbidden status
    if (function_exists('http_response_code')) {
        http_response_code(403);
    }
    echo "This script can only be run from the command line.\n";
    exit;
}

$filename = $argv[1] ?? null;
if (!$filename) {
    echo 'Filename missing';
    exit;
}

$configfile = __DIR__ . "/config/$filename.json";
if (!file_exists($configfile)) {
    echo "status  => error\n";
    echo "message => File does not exits.\n";
    exit;
}

$input = @file_get_contents($configfile);
$args  = json_decode($input);

if (json_last_error() !== JSON_ERROR_NONE || !is_object($args)) {
    echo "status  => error\n";
    echo "message => Invalid JSON payload.\n";
    exit;
}

if (!isset($args->package) || !is_string($args->package)) {
    echo "status  => error\n";
    echo "message => File is missing or invalid 'package' parameter.\n";
    exit;
}

if (!isset($args->controller) || !is_string($args->controller)) {
    echo "status  => error\n";
    echo "message => File is missing or invalid 'controller' parameter.\n";
    exit;
}

$packageClean = preg_replace('/[^a-zA-Z0-9_]/', '', $args->package);
$ctrlClean    = preg_replace('/[^a-zA-Z0-9_]/', '', $args->controller);
$ctrlPascal   = str_replace(' ', '', ucwords(str_replace('_', ' ', $ctrlClean)));
$ctrlPascal   = str_replace('Controller', '', $ctrlPascal) . 'Controller';

// Build the full namespaced class name dynamically as a string
// e.g., "EventLab\System\Controllers\MigrateController"
$className = 'EventLab\\' . ucfirst($packageClean) . '\\Controllers\\' . $ctrlPascal;

require_once __DIR__ . '/autoloader.php';

try {
    // class_exists() will automatically trigger the autoloader behind the scenes!
    if (!class_exists($className)) {
        echo "status  => error\n";
        echo "message => Controller class '$className' could not be loaded.\n";
        exit;
    }

    // 3. Instantiate using the dynamic string name
    $instance = new $className();

    if (!method_exists($instance, 'handle')) {
        echo "status  => error\n";
        echo "message => Method 'handle' not found in class '$className'.\n";
        exit;
    }

    // 4. Run it exactly like you were doing
    $response = $instance->handle($args);

    if (gettype($response) === 'array') {
        foreach ($response as $key => $value) {
            echo "$key => $value\n";
        }
    } else {
        echo $response . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "status        => error\n";
    echo "message       => Internal Server Error. \n";
    echo 'error_details => ' . $e->getMessage() . "\n";
}
