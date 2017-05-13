<?php

// micro autoloader
spl_autoload_register(function($class) {

    if (0 === strpos('C0DE8', $class)) {
        require __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
        return true;
    }

    return false;
});
