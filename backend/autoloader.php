<?php

// 1. Build the master prefix map by scanning the packages folder
$prefixMap  = [];
$packageDir = __DIR__ . '/packages';

if (is_dir($packageDir)) {
    // Scan the directory and filter out "." and ".."
    $packages = array_diff(scandir($packageDir), ['.', '..']);

    foreach ($packages as $package) {
        $packageAutoloadFile = $packageDir . '/' . $package . '/autoloader.php';

        // If the package has defined its own mapper, merge it!
        if (file_exists($packageAutoloadFile)) {
            $packageMapping = require $packageAutoloadFile;
            if (is_array($packageMapping)) {
                $prefixMap = array_merge($prefixMap, $packageMapping);
            }
        }
    }
}

// 2. Register the standard lookup function using our merged map
spl_autoload_register(function ($class) use ($prefixMap) {
    foreach ($prefixMap as $prefix => $baseDir) {
        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file          = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});
