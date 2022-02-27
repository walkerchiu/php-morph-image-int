<?php

/**
 * @license MIT
 * @package WalkerChiu\MorphImage
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Switch association of package to On or Off
    |--------------------------------------------------------------------------
    |
    | When you set someone On:
    |     1. Its Foreign Key Constraints will be created together with data table.
    |     2. You may need to change the corresponding class settings in the config/wk-core.php.
    |
    | When you set someone Off:
    |     1. Association check will not be performed on FormRequest and Observer.
    |     2. Cleaner and Initializer will not handle tasks related to it.
    |
    | Note:
    |     The association still exists, which means you can still access related objects.
    |
    */
    'onoff' => [
        'core-lang_core' => 0,

        'blog'           => 0,
        'group'          => 0,
        'mall-shelf'     => 1,
        'morph-category' => 0,
        'morph-comment'  => 0,
        'rule'           => 0,
        'rule-hit'       => 0,
        'site'           => 0
    ],

    /*
    |--------------------------------------------------------------------------
    | Lang Log
    |--------------------------------------------------------------------------
    |
    | 0: Don't keep data.
    | 1: Keep data.
    |
    */
    'lang_log' => 0,

    /*
    |--------------------------------------------------------------------------
    | Output Data Format from Repository
    |--------------------------------------------------------------------------
    |
    | null:                  Query.
    | query:                 Query.
    | collection:            Query collection.
    | collection_pagination: Query collection with pagination.
    | array:                 Array.
    | array_pagination:      Array with pagination.
    |
    */
    'output_format' => null,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    */
    'pagination' => [
        'pageName' => 'page',
        'perPage'  => 15
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Delete
    |--------------------------------------------------------------------------
    |
    | 0: Disable.
    | 1: Enable.
    |
    */
    'soft_delete' => 1,

    /*
    |--------------------------------------------------------------------------
    | Command
    |--------------------------------------------------------------------------
    |
    | Location of Commands.
    |
    */
    'command' => [
        'cleaner' => 'WalkerChiu\MorphImage\Console\Commands\MorphImageCleaner'
    ],

    /*
    |--------------------------------------------------------------------------
    | Limit images when upload it.
    |--------------------------------------------------------------------------
    |
    | Each value must not be null or zero.
    |
    */
    'images' => [
        'defalut' => [
            'min-width'  => 16,
            'min-height' => 16,
            'max-width'  => 800,
            'max-height' => 600,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'icon' => [
            'min-width'  => 16,
            'min-height' => 16,
            'max-width'  => 48,
            'max-height' => 48,
            'max-size'   => 256,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'logo' => [
            'min-width'  => 64,
            'min-height' => 64,
            'max-width'  => 800,
            'max-height' => 600,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'cover' => [
            'min-width'  => 480,
            'min-height' => 480,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],


        'eye' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'face' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'fingerprint' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'imprint' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'palm' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ],

        'object' => [
            'min-width'  => 240,
            'min-height' => 240,
            'max-width'  => 1200,
            'max-height' => 1200,
            'max-size'   => 3072,
            'mines'      => 'webp,png,svg,jpg,jpeg'
        ]
    ]
];
