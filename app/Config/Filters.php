<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
// Hamara naya AuthFilter aur PermissionFilter niche import ho raha hai
use App\Filters\AuthFilter; 
use App\Filters\PermissionFilter; 

class Filters extends BaseFilters
{
    /**
     * Aliases for Filter classes.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'auth'          => AuthFilter::class,       // Login check ke liye
        'permission'    => PermissionFilter::class, // Module-wise permissions ke liye
    ];

    /**
     * List of special required filters.
     */
    public array $required = [
        'before' => [
            'forcehttps', 
            'pagecache',  
        ],
        'after' => [
            'pagecache',   
            'performance', 
            'toolbar',     
        ],
    ];

    /**
     * Globals are applied before and after every request.
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Particular HTTP method filters.
     */
    public array $methods = [];

    /**
     * Particular URI pattern filters.
     */
    public array $filters = [
        // 1. Common Auth: Admin aur Staff dono ke liye jo pages accessible hain
        'auth' => [
            'before' => [
                'dashboard',     
                'dashboard/*',   
                'clients',       
                'clients/*',     
                'backups/*',
                'orders/*',
                'notifications/*'
            ]
        ],

        // 2. Admin Only Auth: Ye routes sirf Admin (role=admin) ke liye lock hain
        'auth:admin' => [
            'before' => [
                'admin/*',       
                'settings/*',    
                'permissions/*', 
                'staff/*'        
            ]
        ],

        // 3. Permission System: Ye check karega ki staff ko specific module allow hai ya nahi
        'permission' => [
            'before' => [
                'clients', 'clients/*',
                'orders', 'orders/*',
                'backups', 'backups/*',
                'notifications', 'notifications/*'
            ]
        ]
    ];
}