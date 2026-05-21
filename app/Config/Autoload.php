<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
     * Proto-namespaces provide a mechanism for loading classes without
     * needing to scan the filesystem.
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH, // For custom app namespace
        'Config'      => APPPATH . 'Config', // CONFPATH ki jagah APPPATH . 'Config' kar diya hai
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
     * The class map provides a mechanism for mapping class names to
     * specific file paths.
     */
    public $classmap = [];

    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     * The files array allows you to load non-class files, like functions.
     */
    public $files = [];

    /**
     * -------------------------------------------------------------------
     * Helpers
     * -------------------------------------------------------------------
     * Global helpers that will be loaded on every request.
     */
    public $helpers = ['url', 'form', 'activity', 'session']; 
}