<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Documents Disk
    |--------------------------------------------------------------------------
    |
    | This disk is used for player and club document uploads.
    | Set to 'minio' in production for S3-compatible storage.
    |
    */

    'documents_disk' => env('DOCUMENTS_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // MinIO Object Storage (S3-compatible)
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

        // MinIO local development
        'minio' => [
            'driver' => 's3',
            'key' => env('MINIO_ACCESS_KEY', 'minio'),
            'secret' => env('MINIO_SECRET_KEY', 'minio123'),
            'region' => env('MINIO_REGION', 'us-east-1'),
            'bucket' => env('MINIO_BUCKET', 'zifa-connect'),
            'url' => env('MINIO_URL', 'http://localhost:9000'),
            'endpoint' => env('MINIO_ENDPOINT', 'http://localhost:9000'),
            'use_path_style_endpoint' => true,
            'throw' => false,
        ],

        // Documents storage (player/club documents)
        'documents' => [
            'driver' => env('DOCUMENTS_DRIVER', 'local'),
            'root' => storage_path('app/documents'),
            'url' => env('APP_URL').'/documents',
            'visibility' => 'private',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
