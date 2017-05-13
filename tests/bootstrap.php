<?php

// micro autoloader
spl_autoload_register(function($class) {
    require __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
});
