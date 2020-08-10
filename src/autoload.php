<?php

namespace BenMajor\APIDocs;

spl_autoload_register(function($class) {
    
    # Only attempt to autoload if it's part of the current namespace:
    $autoload = substr($class, 0, strlen(__NAMESPACE__)) == __NAMESPACE__;
    
    if( $autoload )
    {
        $include = __DIR__.DIRECTORY_SEPARATOR.trim(
            str_replace('\\', DIRECTORY_SEPARATOR, substr($class, (strlen(__NAMESPACE__) + 1))),
            DIRECTORY_SEPARATOR
        ).'.php';
        
        if( file_exists($include) )
        {
            require_once $include;
        }
    }
});