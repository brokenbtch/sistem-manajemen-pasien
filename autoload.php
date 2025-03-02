<?php

spl_autoload_register(function ($class) {
    // Ubah namespace separator ke directory separator
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Base directory untuk class files
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;
    
    // Path lengkap ke file
    $file = $baseDir . $class . '.php';
    
    // Load file jika ada
    if (file_exists($file)) {
        require $file;
    }
});
