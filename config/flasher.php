<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Notification Library
    |--------------------------------------------------------------------------
    |
    | This option controls the default notification library that will be used
    | by the framework.
    |
    */
    'default' => 'toastr',

    /*
    |--------------------------------------------------------------------------
    | Notification Libraries
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many notification libraries as you wish.
    |
    */
    'libraries' => [
        'toastr' => [
            'scripts' => [
                '/vendor/flasher/toastr.min.js',
            ],
            'styles' => [
                '/vendor/flasher/toastr.min.css',
            ],
            'options' => [
                'closeButton' => true,
                'debug' => false,
                'newestOnTop' => true,
                'progressBar' => true,
                'positionClass' => 'toast-top-right',
                'preventDuplicates' => false,
                'onclick' => null,
                'showDuration' => '300',
                'hideDuration' => '1000',
                'timeOut' => '5000',
                'extendedTimeOut' => '1000',
                'showEasing' => 'swing',
                'hideEasing' => 'linear',
                'showMethod' => 'fadeIn',
                'hideMethod' => 'fadeOut',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Inject Assets
    |--------------------------------------------------------------------------
    |
    | If set to true, the library will automatically inject the required
    | JavaScript and CSS files into your HTML response.
    |
    */
    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Translate Messages
    |--------------------------------------------------------------------------
    |
    | If set to true, the library will automatically translate notification
    | messages using Laravel's translation system.
    |
    */
    'translate' => true,

    /*
    |--------------------------------------------------------------------------
    | Flash Bag
    |--------------------------------------------------------------------------
    |
    | The flash bag is used to store notifications between requests.
    |
    */
    'flash_bag' => [
        'enabled' => true,
        'key' => 'flasher',
    ],

    /*
    |--------------------------------------------------------------------------
    | Filter Criteria
    |--------------------------------------------------------------------------
    |
    | Filter notifications based on specific criteria.
    |
    */
    'filter_criteria' => [],

    /*
    |--------------------------------------------------------------------------
    | Main Script
    |--------------------------------------------------------------------------
    |
    | The main PHPFlasher JavaScript file.
    |
    */
    'main_script' => '/vendor/flasher/flasher.min.js',
];
