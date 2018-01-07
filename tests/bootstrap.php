<?php

// micro autoloader
spl_autoload_register(function($class) {

    if (0 === strpos($class, 'C0DE8')) {
        require __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        return true;
    }

    return false;
});
