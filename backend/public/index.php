<?php

if (php_sapi_name() === 'cli') {
    // If it's accessed via web, return a 403 Forbidden status
    if (function_exists('http_response_code')) {
        http_response_code(403);
    }
    echo "This script can not be run from the command line.\n";
    exit;
}

// 1. CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// 2. Handle Preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 3. Enforce POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Use POST.']);
    exit;
}

// 4. Parse payload
$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if (strpos($contentType, 'multipart/form-data') !== false) {
    // For file uploads via FormData
    $args         = (object) $_POST;
    $args->_files = $_FILES;
} else {
    // Default JSON payload
    $input = file_get_contents('php://input');
    $args  = json_decode($input);

    if (json_last_error() !== JSON_ERROR_NONE || ! is_object($args)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload.']);
        exit;
    }
}

if (! isset($args->package) || ! is_string($args->package)) {
    echo json_encode(['status' => 'error', 'message' => "File is missing or invalid 'package' parameter."]);
    exit;
}

if (! isset($args->controller) || ! is_string($args->controller)) {
    echo json_encode(['status' => 'error', 'message' => "File is missing or invalid 'controller' parameter."]);
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
    if (! class_exists($className)) {
        echo json_encode([
            'status'  => 'error',
            'message' => "Controller class '$className' could not be loaded.",
        ]);
        exit;
    }

    // 3. Instantiate using the dynamic string name
    $instance = new $className();

    if (! method_exists($instance, 'handle')) {
        echo json_encode(['status' => 'error', 'message' => "Method 'handle' not found in class '$className'."]);
        exit;
    }

    // 4. Run it exactly like you were doing
    $response = $instance->handle($args);
    echo json_encode($response);
    exit;
} catch (\Exception $e) {
    echo json_encode([
        'status'        => 'error',
        'message'       => 'Internal Server Error.',
        'error_details' => $e->getMessage(),
    ]);
}
