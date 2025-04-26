<?php
spl_autoload_register(function ($class) {
    // Direktori yang berisi file kelas
    $directories = [
        'models/',
        'controllers/',
        'config/'
    ];
    
    // Cek setiap direktori
    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
?>