<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => env('HASH_DRIVER', 'argon2id'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
        'verify' => env('HASH_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon2ID Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon2ID algorithm. Argon2ID is the
    | recommended variant as it provides resistance against both side-channel
    | and time-memory trade-off attacks.
    |
    */

    'argon2id' => [
        'memory' => env('ARGON2ID_MEMORY', 131072), // 128 MB
        'threads' => env('ARGON2ID_THREADS', 4),
        'time' => env('ARGON2ID_TIME', 6),
        'verify' => env('HASH_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | RISDA Custom Salt Configuration
    |--------------------------------------------------------------------------
    |
    | Custom salt configuration for RISDA system. This adds an additional
    | layer of security by using application-specific salt.
    |
    */

    'risda_salt' => [
        'enabled' => env('RISDA_SALT_ENABLED', true),
        'pepper' => env('RISDA_PEPPER', 'RISDA_SECURE_PEPPER_2024'),
        'rounds' => env('RISDA_SALT_ROUNDS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehash On Login
    |--------------------------------------------------------------------------
    |
    | Setting this option to true will tell Laravel to automatically rehash
    | the user's password during login if the configured work factor for
    | the algorithm has changed, allowing graceful upgrades of hashes.
    |
    */

    'rehash_on_login' => true,

];
